<?php

namespace App\Application\UseCases\Invoice;

use App\Application\DTOs\CreateInvoiceDTO;
use App\Application\Services\InvoiceCalculatorService;
use App\Domain\Invoice\Events\InvoiceCreated;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Services\InvoiceNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Use Case: Créer une nouvelle facture
 * 
 * Responsabilités:
 * 1. Valider les données
 * 2. Générer le numéro de facture
 * 3. Calculer les totaux
 * 4. Persister en base de données
 * 5. Logger l'action
 */
class CreateInvoiceUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private ClientRepositoryInterface $clientRepository,
        private InvoiceCalculatorService $calculator,
        private InvoiceNumberService $numberService,
    ) {}

    /**
     * Exécuter le cas d'utilisation
     */
    public function execute(CreateInvoiceDTO $dto): Invoice
    {
        // 1. Valider les données
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new \InvalidArgumentException('Validation failed: ' . implode(', ', $errors));
        }

        // 2. Vérifier que le client existe et appartient au tenant
        $client = $this->clientRepository->findById($dto->clientId);
        if (!$client || $client->tenant_id !== $dto->tenantId) {
            throw new \InvalidArgumentException('Client not found or does not belong to this tenant');
        }

        // 3. Calculer les totaux
        $totals = $this->calculator->calculateInvoiceTotals(
            $dto->items,
            $dto->taxRate,
            $dto->discount
        );

        // 4. Générer le numéro de facture
        $invoiceNumber = $this->numberService->generate($dto->tenantId);

        // 5. Préparer les données de la facture
        $invoiceData = [
            'tenant_id' => $dto->tenantId,
            'user_id' => $dto->userId,
            'client_id' => $dto->clientId,
            'number' => $invoiceNumber,
            'type' => $dto->type,
            'status' => 'pending',
            'subtotal' => $totals['subtotal'],
            'tax' => $totals['tax'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
            'currency' => $dto->currency,
            'issued_at' => $dto->issuedAt ?? now(),
            'due_date' => $dto->dueDate ?? now()->addDays(30),
            'notes' => $dto->notes,
            'terms' => $dto->terms,
        ];

        // 6. Préparer les items avec totaux calculés
        $items = [];
        foreach ($dto->items as $item) {
            $itemTotal = $this->calculator->calculateItemTotal($item);
            $items[] = [
                'tenant_id' => $dto->tenantId,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? $dto->taxRate ?? 0,
                'discount' => $item['discount'] ?? 0,
                'total' => $itemTotal,
            ];
        }

        $invoiceData['items'] = $items;

        try {
            // 7. Persister en base de données (transaction)
            $invoice = $this->invoiceRepository->create($invoiceData);

            // 8. Logger l'action
            Log::info('Invoice created', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
                'tenant_id' => $invoice->tenant_id,
                'client_id' => $invoice->client_id,
                'total' => $invoice->total,
            ]);

            // 9. Dispatch event pour déclencher notifications et génération PDF
            event(new InvoiceCreated($invoice));

            return $invoice;

        } catch (\Exception $e) {
            Log::error('Failed to create invoice', [
                'error' => $e->getMessage(),
                'tenant_id' => $dto->tenantId,
                'client_id' => $dto->clientId,
            ]);

            throw new \RuntimeException('Failed to create invoice: ' . $e->getMessage(), 0, $e);
        }
    }
}
