<?php

namespace App\Actions\Invoice;

use App\Domain\Invoice\Models\Invoice;
use App\Services\InvoiceCalculatorService;
use App\Services\InvoiceNumberService;
use Illuminate\Support\Str;

class CreateInvoiceAction
{
    public function __construct(
        private InvoiceCalculatorService $calculator,
        private InvoiceNumberService $numberService
    ) {}

    public function execute(array $data): Invoice
    {
        // Generate invoice number if not provided
        if (!isset($data['number'])) {
            $data['number'] = $this->numberService->generate();
        }

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = (string) Str::uuid();
        }

        // Create invoice
        $invoice = Invoice::create([
            'client_id' => $data['client_id'],
            'number' => $data['number'],
            'uuid' => $data['uuid'],
            'issued_at' => $data['issue_date'] ?? $data['issued_at'] ?? now(),
            'due_date' => $data['due_date'] ?? now()->addDays(30),
            'status' => $data['status'] ?? 'pending',
            'currency' => $data['currency'] ?? 'USD',
            'notes' => $data['notes'] ?? null,
            'subtotal' => 0,
            'tax' => 0,
            'discount' => 0,
            'total' => 0,
        ]);

        // Add invoice items
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        }

        // Recalculate totals
        $this->calculator->calculate($invoice);

        return $invoice->fresh(['items', 'client']);
    }
}
