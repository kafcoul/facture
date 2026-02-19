<?php

namespace App\Notifications;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification envoyée au propriétaire quand une facture est en retard
 */
class InvoiceOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [60, 300];

    public function __construct(
        public Invoice $invoice,
        public int $daysOverdue = 0
    ) {}

    /**
     * Canaux de notification
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Notification email
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invoice = $this->invoice->load('client');
        $clientName = $invoice->client->name ?? 'Client';
        $amount = number_format($invoice->total, 0, ',', ' ') . ' ' . ($invoice->currency ?? 'XOF');
        $dueDate = $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A';

        $urgencyEmoji = $this->daysOverdue > 30 ? '🔴' : ($this->daysOverdue > 14 ? '🟠' : '🟡');
        $urgencyText = $this->daysOverdue > 30 ? 'Retard critique' : ($this->daysOverdue > 14 ? 'Retard important' : 'Retard');

        return (new MailMessage)
            ->subject("{$urgencyEmoji} {$urgencyText} : Facture {$invoice->number} — {$this->daysOverdue} jours")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("La facture **{$invoice->number}** est en retard de **{$this->daysOverdue} jour(s)**.")
            ->line("**Client :** {$clientName}")
            ->line("**Montant dû :** {$amount}")
            ->line("**Échéance :** {$dueDate}")
            ->line("Nous vous recommandons de relancer votre client pour obtenir le paiement.")
            ->action('Voir la facture', url("/client/invoices/{$invoice->id}"))
            ->line("💡 **Astuce :** Envoyez un rappel directement depuis votre tableau de bord.")
            ->salutation('— L\'équipe InvoiceSaaS');
    }

    /**
     * Notification base de données
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_overdue',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->number,
            'client_name' => $this->invoice->client->name ?? null,
            'amount' => $this->invoice->total,
            'currency' => $this->invoice->currency ?? 'XOF',
            'days_overdue' => $this->daysOverdue,
            'due_date' => $this->invoice->due_date?->toDateString(),
            'message' => "Facture {$this->invoice->number} en retard de {$this->daysOverdue} jours ({$this->invoice->total} " . ($this->invoice->currency ?? 'XOF') . ')',
        ];
    }
}
