<?php

namespace App\Domain\Invoice\Events;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event déclenché lors de la création d'une facture
 */
class InvoiceCreated
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
            'invoice:created',
            'invoice:' . $this->invoice->id,
            'tenant:' . $this->invoice->tenant_id,
        ];
    }
}
