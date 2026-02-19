<?php

namespace App\Application\Listeners\Payment;

use App\Domain\Payment\Events\PaymentReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Logger tous les événements de paiement
 */
class LogPaymentEvent implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 5;

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment->load(['invoice', 'invoice.client']);

        Log::info('Payment received - detailed log', [
            'event' => 'payment.received',
            'payment_id' => $payment->id,
            'invoice_id' => $payment->invoice_id,
            'invoice_number' => $payment->invoice->number,
            'tenant_id' => $payment->tenant_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'gateway' => $payment->gateway,
            'transaction_id' => $payment->transaction_id,
            'client_email' => $payment->invoice->client->email,
            'payment_method' => $payment->payment_method,
            'completed_at' => $payment->completed_at?->toIso8601String(),
            'metadata' => $payment->metadata,
        ]);

        // TODO: Intégration avec système d'analytics externe
        // Analytics::track('payment.received', [
        //     'tenant_id' => $payment->tenant_id,
        //     'amount' => $payment->amount,
        //     'gateway' => $payment->gateway,
        // ]);
    }
}
