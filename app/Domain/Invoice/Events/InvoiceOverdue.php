<?php

namespace App\Domain\Invoice\Events;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event déclenché quand une facture dépasse sa date d'échéance
 */
class InvoiceOverdue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public int $daysOverdue
    ) {}

    /**
     * Obtenir les tags pour les logs
     */
    public function tags(): array
    {
        return [
            'invoice:overdue',
            'invoice:' . $this->invoice->id,
            'tenant:' . $this->invoice->tenant_id,
            'days:' . $this->daysOverdue,
        ];
    }
}
