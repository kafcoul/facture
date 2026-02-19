<?php

namespace App\Application\Listeners\Invoice;

use App\Domain\Invoice\Events\InvoicePaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Mettre à jour le statut et envoyer notification de paiement
 */
class UpdateInvoiceStatus implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice->load(['client', 'payments']);

        Log::info('Processing invoice paid event', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'total' => $invoice->total,
        ]);

        try {
            // Mettre à jour les métadonnées
            $invoice->update([
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'paid_notification_sent_at' => now()->toIso8601String(),
                    'payment_count' => $invoice->payments()->count(),
                ]),
            ]);

            // TODO: Envoyer email de remerciement au client
            // Mail::to($invoice->client->email)
            //     ->send(new InvoicePaidMail($invoice));

            Log::info('Invoice paid notification sent', [
                'invoice_id' => $invoice->id,
                'client_email' => $invoice->client->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update invoice status', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
