<?php

namespace App\Mail;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public float $amount;
    public string $gateway;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, float $amount, string $gateway = 'stripe')
    {
        $this->invoice = $invoice;
        $this->amount = $amount;
        $this->gateway = $gateway;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Paiement reçu — Facture {$this->invoice->number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-received',
            with: [
                'invoice' => $this->invoice,
                'clientName' => $this->invoice->client->name ?? 'Client',
                'invoiceNumber' => $this->invoice->number,
                'amount' => number_format($this->amount, 0, ',', ' ') . ' ' . $this->invoice->currency,
                'total' => number_format($this->invoice->total, 0, ',', ' ') . ' ' . $this->invoice->currency,
                'gateway' => ucfirst($this->gateway),
                'paidAt' => now()->format('d/m/Y à H:i'),
                'invoiceUrl' => route('invoices.public', $this->invoice->uuid),
            ],
        );
    }
}
