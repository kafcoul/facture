<?php

namespace App\Domain\Payment\Events;

use App\Domain\Payment\Models\Payment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event déclenché quand un paiement est reçu avec succès
 */
class PaymentReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Payment $payment
    ) {}

    /**
     * Obtenir les tags pour les logs
     */
    public function tags(): array
    {
        return [
            'payment:received',
            'payment:' . $this->payment->id,
            'invoice:' . $this->payment->invoice_id,
            'tenant:' . $this->payment->tenant_id,
            'gateway:' . $this->payment->gateway,
        ];
    }
}
