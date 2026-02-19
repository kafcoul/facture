<?php

namespace App\Application\Listeners\Payment;

use App\Domain\Payment\Events\PaymentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Gérer les paiements échoués
 */
class HandlePaymentFailure implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 2;

    /**
     * Handle the event.
     */
    public function handle(PaymentFailed $event): void
    {
        $payment = $event->payment->load(['invoice', 'invoice.client']);

        Log::error('Payment failed', [
            'payment_id' => $payment->id,
            'invoice_id' => $payment->invoice_id,
            'invoice_number' => $payment->invoice->number,
            'amount' => $payment->amount,
            'gateway' => $payment->gateway,
            'reason' => $event->reason,
            'client_email' => $payment->invoice->client->email,
        ]);

        try {
            // TODO: Envoyer email au client avec instructions
            // Mail::to($payment->invoice->client->email)
            //     ->send(new PaymentFailedMail($payment, $event->reason));

            // Incrémenter le compteur d'échecs
            $metadata = $payment->metadata ?? [];
            $failureCount = ($metadata['failure_count'] ?? 0) + 1;

            $payment->update([
                'metadata' => array_merge($metadata, [
                    'failure_count' => $failureCount,
                    'last_failure_at' => now()->toIso8601String(),
                    'last_failure_reason' => $event->reason,
                ]),
            ]);

            // TODO: Notifier l'administrateur si trop d'échecs
            if ($failureCount >= 3) {
                Log::critical('Multiple payment failures detected', [
                    'payment_id' => $payment->id,
                    'failure_count' => $failureCount,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle payment failure', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
