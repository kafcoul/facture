<?php

namespace App\Application\Listeners\Invoice;

use App\Domain\Invoice\Events\InvoiceOverdue;
use App\Notifications\InvoiceOverdueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Envoyer rappel pour facture en retard
 */
class SendOverdueReminder implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 2;

    /**
     * Handle the event.
     */
    public function handle(InvoiceOverdue $event): void
    {
        $invoice = $event->invoice->load(['client', 'user']);

        Log::warning('Invoice overdue', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'days_overdue' => $event->daysOverdue,
            'client_email' => $invoice->client->email ?? null,
        ]);

        try {
            // Notifier le propriétaire de la facture
            if ($invoice->user) {
                $invoice->user->notify(new InvoiceOverdueNotification($invoice, $event->daysOverdue));
            }

            // Incrémenter le compteur de rappels
            $metadata = $invoice->metadata ?? [];
            $reminderCount = ($metadata['reminder_count'] ?? 0) + 1;

            $invoice->update([
                'metadata' => array_merge($metadata, [
                    'reminder_count' => $reminderCount,
                    'last_reminder_sent_at' => now()->toIso8601String(),
                    'last_overdue_milestone' => $event->daysOverdue,
                ]),
            ]);

            Log::info('Overdue reminder sent', [
                'invoice_id' => $invoice->id,
                'reminder_count' => $reminderCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send overdue reminder', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
