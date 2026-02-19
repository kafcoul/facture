<?php

namespace App\Jobs;

use App\Domain\Invoice\Models\Invoice;
use App\Notifications\InvoiceSentToClientNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];

    public function __construct(
        public Invoice $invoice
    ) {}

    public function handle(): void
    {
        $this->invoice->load(['client', 'user', 'tenant', 'items']);

        $clientEmail = $this->invoice->client->email ?? null;

        if (!$clientEmail) {
            Log::warning('Cannot send invoice email: client has no email', [
                'invoice_id' => $this->invoice->id,
            ]);
            return;
        }

        try {
            // Envoyer la notification au client via mail on-demand
            Notification::route('mail', $clientEmail)
                ->notify(new InvoiceSentToClientNotification($this->invoice));

            // Mettre à jour les métadonnées
            $this->invoice->update([
                'metadata' => array_merge($this->invoice->metadata ?? [], [
                    'email_sent_at' => now()->toIso8601String(),
                    'email_sent_to' => $clientEmail,
                ]),
            ]);

            Log::info('Invoice email sent to client', [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->number,
                'recipient' => $clientEmail,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send invoice email', [
                'invoice_id' => $this->invoice->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('SendInvoiceEmailJob failed after all retries', [
            'invoice_id' => $this->invoice->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
