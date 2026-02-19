<?php

namespace App\Application\UseCases\Payment;

use App\Application\DTOs\ProcessPaymentDTO;
use App\Application\Services\InvoiceCalculatorService;
use App\Domain\Invoice\Events\InvoicePaid;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use App\Domain\Payment\Events\PaymentFailed;
use App\Domain\Payment\Events\PaymentReceived;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Repositories\PaymentRepositoryInterface;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Use Case: Traiter un paiement
 * 
 * Responsabilités:
 * 1. Valider le paiement
 * 2. Vérifier la facture
 * 3. Initier le paiement avec la gateway
 * 4. Créer l'enregistrement de paiement
 * 5. Retourner les informations de redirection
 */
class ProcessPaymentUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private InvoiceCalculatorService $calculator,
        private PaymentGatewayService $gatewayService,
    ) {}

    /**
     * Exécuter le cas d'utilisation
     * 
     * @return array ['payment' => Payment, 'redirect_url' => string|null]
     */
    public function execute(ProcessPaymentDTO $dto): array
    {
        // 1. Valider les données
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new \InvalidArgumentException('Validation failed: ' . implode(', ', $errors));
        }

        // 2. Charger la facture
        $invoice = Invoice::with(['client', 'payments'])->findOrFail($dto->invoiceId);

        // 3. Vérifier que la facture n'est pas déjà payée
        if ($invoice->isPaid()) {
            throw new \InvalidArgumentException('Invoice is already paid');
        }

        // 4. Calculer le montant restant à payer
        $alreadyPaid = $invoice->payments()
            ->where('status', 'completed')
            ->sum('amount');

        $remainingAmount = $invoice->total - $alreadyPaid;

        // 5. Valider le montant du paiement
        if (!$this->calculator->validatePaymentAmount($invoice->total, $dto->amount, $alreadyPaid)) {
            throw new \InvalidArgumentException(
                "Invalid payment amount. Remaining: {$remainingAmount}, Requested: {$dto->amount}"
            );
        }

        try {
            return DB::transaction(function () use ($dto, $invoice, $remainingAmount) {
                // 6. Créer l'enregistrement de paiement avec status 'pending'
                $payment = $this->paymentRepository->create([
                    'tenant_id' => $invoice->tenant_id,
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'amount' => $dto->amount,
                    'gateway' => $dto->gateway,
                    'transaction_id' => Str::uuid(),
                    'status' => 'pending',
                    'currency' => $dto->currency,
                    'payment_method' => $dto->paymentMethod,
                    'metadata' => array_merge($dto->metadata, [
                        'invoice_number' => $invoice->number,
                        'client_email' => $invoice->client->email,
                    ]),
                ]);

                // 7. Initier le paiement avec la gateway
                $gatewayResponse = $this->gatewayService->initiatePayment(
                    gateway: $dto->gateway,
                    amount: $dto->amount,
                    currency: $dto->currency,
                    invoiceId: $invoice->id,
                    clientEmail: $invoice->client->email,
                    returnUrl: $dto->returnUrl,
                    metadata: [
                        'payment_id' => $payment->id,
                        'invoice_number' => $invoice->number,
                    ]
                );

                // 8. Mettre à jour le paiement avec l'ID de transaction de la gateway
                if (isset($gatewayResponse['transaction_id'])) {
                    $this->paymentRepository->update($payment, [
                        'transaction_id' => $gatewayResponse['transaction_id'],
                        'metadata' => array_merge($payment->metadata, [
                            'gateway_response' => $gatewayResponse,
                        ]),
                    ]);
                }

                // 9. Logger l'action
                Log::info('Payment initiated', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                    'gateway' => $dto->gateway,
                    'amount' => $dto->amount,
                    'transaction_id' => $gatewayResponse['transaction_id'] ?? null,
                ]);

                // 10. Retourner le paiement et l'URL de redirection
                return [
                    'payment' => $payment->fresh(),
                    'redirect_url' => $gatewayResponse['redirect_url'] ?? null,
                    'gateway_response' => $gatewayResponse,
                ];
            });

        } catch (\Exception $e) {
            Log::error('Failed to process payment', [
                'invoice_id' => $dto->invoiceId,
                'gateway' => $dto->gateway,
                'amount' => $dto->amount,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to process payment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Confirmer un paiement après callback de la gateway
     */
    public function confirmPayment(int $paymentId, array $gatewayData): Payment
    {
        $payment = Payment::with(['invoice'])->findOrFail($paymentId);

        try {
            return DB::transaction(function () use ($payment, $gatewayData) {
                // Vérifier le paiement avec la gateway
                $verified = $this->gatewayService->verifyPayment(
                    $payment->gateway,
                    $gatewayData
                );

                if ($verified) {
                    // Marquer le paiement comme complété
                    $payment->markAsCompleted();

                    // Vérifier si la facture est maintenant totalement payée
                    $invoice = $payment->invoice;
                    $totalPaid = $invoice->payments()
                        ->where('status', 'completed')
                        ->sum('amount');

                    if ($totalPaid >= $invoice->total) {
                        $invoice->markAsPaid();
                        
                        // Dispatch event pour facture payée
                        event(new InvoicePaid($invoice));
                    }

                    Log::info('Payment confirmed', [
                        'payment_id' => $payment->id,
                        'invoice_id' => $invoice->id,
                        'amount' => $payment->amount,
                    ]);

                    // Dispatch event pour paiement reçu
                    event(new PaymentReceived($payment));
                }

                return $payment->fresh();
            });

        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());

            Log::error('Payment confirmation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            // Dispatch event pour paiement échoué
            event(new PaymentFailed($payment, $e->getMessage()));

            throw $e;
        }
    }
}
