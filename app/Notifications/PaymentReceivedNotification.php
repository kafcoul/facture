<?php

namespace App\Notifications;

use App\Domain\Payment\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification envoyée au propriétaire quand un paiement est reçu
 */
class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];

    public function __construct(
        public Payment $payment
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
        $payment = $this->payment->load(['invoice', 'invoice.client']);
        $invoice = $payment->invoice;
        $clientName = $invoice->client->name ?? 'Client';
        $amount = number_format($payment->amount, 0, ',', ' ') . ' ' . ($payment->currency ?? 'XOF');
        $method = $this->getPaymentMethodLabel($payment->payment_method ?? $payment->gateway ?? 'inconnu');

        return (new MailMessage)
            ->subject("💰 Paiement reçu — {$amount} (Facture {$invoice->number})")
            ->greeting("Bonjour {$notifiable->name} !")
            ->line("Excellent ! Un paiement a été reçu.")
            ->line("**Montant :** {$amount}")
            ->line("**Facture :** {$invoice->number}")
            ->line("**Client :** {$clientName}")
            ->line("**Méthode :** {$method}")
            ->line("**Date :** " . now()->format('d/m/Y à H:i'))
            ->action('Voir les paiements', url('/client/payments'))
            ->line('Votre facture a été automatiquement marquée comme payée.')
            ->salutation('— L\'équipe InvoiceSaaS');
    }

    /**
     * Notification base de données
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_received',
            'payment_id' => $this->payment->id,
            'invoice_id' => $this->payment->invoice_id,
            'invoice_number' => $this->payment->invoice->number ?? null,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency ?? 'XOF',
            'payment_method' => $this->payment->payment_method ?? $this->payment->gateway ?? null,
            'message' => 'Paiement de ' . number_format($this->payment->amount, 0, ',', ' ') . ' ' . ($this->payment->currency ?? 'XOF') . ' reçu',
        ];
    }

    /**
     * Label lisible pour la méthode de paiement
     */
    private function getPaymentMethodLabel(string $method): string
    {
        return match ($method) {
            'stripe' => 'Carte bancaire (Stripe)',
            'paystack' => 'Paystack',
            'flutterwave' => 'Flutterwave',
            'wave' => 'Wave',
            'orange_money' => 'Orange Money',
            'mtn_momo' => 'MTN Mobile Money',
            'mpesa' => 'M-Pesa',
            'fedapay' => 'FedaPay',
            'kkiapay' => 'KkiaPay',
            'cinetpay' => 'CinetPay',
            'bank_transfer' => 'Virement bancaire',
            'cash' => 'Espèces',
            default => ucfirst($method),
        };
    }
}
