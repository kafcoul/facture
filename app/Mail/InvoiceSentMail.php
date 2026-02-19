<?php

namespace App\Mail;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceSentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouvelle facture {$this->invoice->number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-sent',
            with: [
                'invoice' => $this->invoice,
                'clientName' => $this->invoice->client->name ?? 'Client',
                'invoiceNumber' => $this->invoice->number,
                'total' => number_format($this->invoice->total, 0, ',', ' ') . ' ' . $this->invoice->currency,
                'dueDate' => $this->invoice->due_date?->format('d/m/Y') ?? '—',
                'issuedAt' => $this->invoice->issued_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
                'paymentUrl' => route('invoices.public', $this->invoice->uuid),
                'downloadUrl' => route('invoices.download', $this->invoice->uuid),
            ],
        );
    }
}
