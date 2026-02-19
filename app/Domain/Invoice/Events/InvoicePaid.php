<?php

namespace App\Domain\Invoice\Events;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event déclenché quand une facture est payée
 */
class InvoicePaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Invoice $invoice
    ) {}

    /**
     * Obtenir les tags pour les logs
     */
    public function tags(): array
    {
        return [
            'invoice:paid',
            'invoice:' . $this->invoice->id,
            'tenant:' . $this->invoice->tenant_id,
        ];
    }
}
