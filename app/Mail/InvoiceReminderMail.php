<?php

namespace App\Mail;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public int $daysOverdue;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->daysOverdue = $invoice->due_date
            ? (int) now()->diffInDays($invoice->due_date, false)
            : 0;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->daysOverdue > 0
            ? "Rappel : Facture {$this->invoice->number} à régler"
            : "Relance : Facture {$this->invoice->number} en retard de " . abs($this->daysOverdue) . " jours";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-reminder',
            with: [
                'invoice' => $this->invoice,
                'clientName' => $this->invoice->client->name ?? 'Client',
                'invoiceNumber' => $this->invoice->number,
                'total' => number_format($this->invoice->total, 0, ',', ' ') . ' ' . $this->invoice->currency,
                'dueDate' => $this->invoice->due_date?->format('d/m/Y') ?? '—',
                'daysOverdue' => abs($this->daysOverdue),
                'isOverdue' => $this->daysOverdue < 0,
                'paymentUrl' => route('invoices.public', $this->invoice->uuid),
            ],
        );
    }
}
