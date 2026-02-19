<?php

namespace App\Notifications;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification envoyée au propriétaire quand une facture est créée
 */
class InvoiceCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];

    public function __construct(
        public Invoice $invoice
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

        return (new MailMessage)
            ->subject("📄 Facture {$invoice->number} créée — {$amount}")
            ->greeting("Bonjour {$notifiable->name} !")
            ->line("Une nouvelle facture a été créée avec succès.")
            ->line("**Facture :** {$invoice->number}")
            ->line("**Client :** {$clientName}")
            ->line("**Montant :** {$amount}")
            ->line("**Date d'échéance :** " . ($invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'Non définie'))
            ->action('Voir la facture', url("/client/invoices/{$invoice->id}"))
            ->line("Vous pouvez envoyer cette facture à votre client depuis votre tableau de bord.")
            ->salutation('— L\'équipe InvoiceSaaS');
    }

    /**
     * Notification base de données
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_created',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->number,
            'client_name' => $this->invoice->client->name ?? null,
            'amount' => $this->invoice->total,
            'currency' => $this->invoice->currency ?? 'XOF',
            'message' => "Facture {$this->invoice->number} créée pour " . number_format($this->invoice->total, 0, ',', ' ') . ' ' . ($this->invoice->currency ?? 'XOF'),
        ];
    }
}
