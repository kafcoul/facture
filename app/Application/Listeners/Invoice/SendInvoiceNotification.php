<?php

namespace App\Application\Listeners\Invoice;

use App\Domain\Invoice\Events\InvoiceCreated;
use App\Notifications\InvoiceCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Envoyer notification email lors de la création d'une facture
 * 
 * ShouldQueue: Exécuté en arrière-plan via les queues
 */
class SendInvoiceNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Nombre de tentatives maximum
     */
    public $tries = 3;

    /**
     * Délai entre les tentatives (secondes)
     */
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    /**
     * Handle the event.
     */
    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice->load(['client', 'items', 'user']);

        Log::info('Sending invoice notification', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'client_email' => $invoice->client->email ?? null,
        ]);

        try {
            // Notifier le propriétaire (l'utilisateur qui a créé la facture)
            if ($invoice->user) {
                $invoice->user->notify(new InvoiceCreatedNotification($invoice));
            }

            Log::info('Invoice notification sent successfully', [
                'invoice_id' => $invoice->id,
                'recipient' => $invoice->user->email ?? null,
            ]);

            // Marquer la facture comme notifiée
            $invoice->update([
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'notification_sent_at' => now()->toIso8601String(),
                ]),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send invoice notification', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Gérer l'échec après toutes les tentatives
     */
    public function failed(InvoiceCreated $event, \Throwable $exception): void
    {
        Log::critical('Invoice notification failed after all retries', [
            'invoice_id' => $event->invoice->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
