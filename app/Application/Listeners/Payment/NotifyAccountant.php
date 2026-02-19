<?php

namespace App\Application\Listeners\Payment;

use App\Domain\Payment\Events\PaymentReceived;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Notifier le propriétaire (et comptable) des paiements reçus
 */
class NotifyAccountant implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment->load(['invoice', 'invoice.client', 'invoice.user', 'tenant']);

        Log::info('Processing payment notification', [
            'payment_id' => $payment->id,
            'invoice_number' => $payment->invoice->number ?? null,
            'amount' => $payment->amount,
        ]);

        try {
            // Toujours notifier le propriétaire de la facture
            $owner = $payment->invoice->user ?? null;
            if ($owner) {
                $owner->notify(new PaymentReceivedNotification($payment));
            }

            // Pour les gros montants (> 1M XOF), notifier aussi les admins du tenant
            if ($payment->amount >= 1000000) {
                $admins = User::where('tenant_id', $payment->tenant_id)
                    ->where('role', 'admin')
                    ->where('id', '!=', $owner?->id)
                    ->get();

                foreach ($admins as $admin) {
                    $admin->notify(new PaymentReceivedNotification($payment));
                }

                Log::info('Large payment: admins notified', [
                    'payment_id' => $payment->id,
                    'admin_count' => $admins->count(),
                ]);
            }

            Log::info('Payment notification sent successfully', [
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
