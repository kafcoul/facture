<?php

namespace App\Notifications;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification email envoyée AU CLIENT avec la facture
 * (Ceci est l'email que le client final reçoit)
 */
class InvoiceSentToClientNotification extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    /**
     * Email envoyé au client
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invoice = $this->invoice->load(['user', 'tenant', 'items']);
        $senderName = $invoice->tenant->name ?? $invoice->user->company_name ?? $invoice->user->name ?? 'InvoiceSaaS';
        $amount = number_format($invoice->total, 0, ',', ' ') . ' ' . ($invoice->currency ?? 'XOF');
        $dueDate = $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'À réception';

        // Lien public vers la facture (avec paiement en ligne)
        $publicUrl = $invoice->public_hash
            ? url("/invoices/{$invoice->uuid}")
            : url("/invoices/{$invoice->id}");

        return (new MailMessage)
            ->subject("Facture {$invoice->number} de {$senderName} — {$amount}")
            ->greeting("Bonjour,")
            ->line("Vous avez reçu une facture de **{$senderName}**.")
            ->line("**Facture N° :** {$invoice->number}")
            ->line("**Montant :** {$amount}")
            ->line("**Échéance :** {$dueDate}")
            ->action('Voir et payer la facture', $publicUrl)
            ->line("Vous pouvez consulter, télécharger et payer cette facture en ligne en toute sécurité.")
            ->line("---")
            ->line("_Cet email a été envoyé via InvoiceSaaS pour le compte de {$senderName}._")
            ->salutation("Cordialement,\n{$senderName}");
    }
}
