<?php

namespace App\Jobs;

use App\Domain\Invoice\Events\InvoiceOverdue;
use App\Domain\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: Vérifier les factures en retard et dispatch events
 * 
 * À exécuter quotidiennement via le scheduler
 */
class CheckOverdueInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting overdue invoices check');

        // Récupérer toutes les factures en retard
        $overdueInvoices = Invoice::query()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->with(['client', 'tenant'])
            ->get();

        $count = 0;

        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date);

            // Dispatch l'événement seulement si c'est un "jalon"
            // Ex: 1 jour, 7 jours, 14 jours, 30 jours
            if ($this->shouldNotify($daysOverdue, $invoice->metadata ?? [])) {
                event(new InvoiceOverdue($invoice, $daysOverdue));
                $count++;

                Log::info('Dispatched overdue event', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                    'days_overdue' => $daysOverdue,
                ]);
            }
        }

        Log::info('Overdue invoices check completed', [
            'total_overdue' => $overdueInvoices->count(),
            'notifications_sent' => $count,
        ]);
    }

    /**
     * Déterminer si on doit envoyer une notification
     * 
     * Envoie uniquement aux jalons: 1, 7, 14, 30 jours
     * Et évite les doublons via metadata
     */
    private function shouldNotify(int $daysOverdue, array $metadata): bool
    {
        // Jalons de notification
        $milestones = [1, 7, 14, 30];

        if (!in_array($daysOverdue, $milestones)) {
            return false;
        }

        // Vérifier si déjà notifié pour ce jalon
        $lastNotified = $metadata['last_overdue_milestone'] ?? 0;

        return $daysOverdue > $lastNotified;
    }
}
