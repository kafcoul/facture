<?php

namespace App\Application\Listeners\Invoice;

use App\Application\UseCases\Invoice\GeneratePdfUseCase;
use App\Domain\Invoice\Events\InvoiceCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: Générer automatiquement le PDF lors de la création d'une facture
 * 
 * Exécuté en arrière-plan
 */
class GenerateInvoicePdf implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 2;
    public $backoff = 30;

    public function __construct(
        private GeneratePdfUseCase $generatePdf
    ) {}

    /**
     * Handle the event.
     */
    public function handle(InvoiceCreated $event): void
    {
        Log::info('Generating PDF for invoice', [
            'invoice_id' => $event->invoice->id,
        ]);

        try {
            $pdfPath = $this->generatePdf->execute($event->invoice->id);

            Log::info('Invoice PDF generated successfully', [
                'invoice_id' => $event->invoice->id,
                'pdf_path' => $pdfPath,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate invoice PDF', [
                'invoice_id' => $event->invoice->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
