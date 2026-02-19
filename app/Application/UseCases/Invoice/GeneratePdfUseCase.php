<?php

namespace App\Application\UseCases\Invoice;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use App\Services\PdfService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Use Case: Générer le PDF d'une facture
 * 
 * Responsabilités:
 * 1. Charger la facture avec relations
 * 2. Générer le PDF via PdfService
 * 3. Sauvegarder le fichier
 * 4. Mettre à jour le chemin dans la DB
 * 5. Retourner le chemin du fichier
 */
class GeneratePdfUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private PdfService $pdfService,
    ) {}

    /**
     * Exécuter le cas d'utilisation
     * 
     * @param int $invoiceId ID de la facture
     * @param bool $forceRegenerate Forcer la régénération même si le PDF existe
     * @return string Chemin du fichier PDF
     */
    public function execute(int $invoiceId, bool $forceRegenerate = false): string
    {
        // 1. Charger la facture avec toutes les relations
        $invoice = Invoice::with(['client', 'items', 'user', 'tenant'])
            ->findOrFail($invoiceId);

        // 2. Vérifier si le PDF existe déjà
        if (!$forceRegenerate && $invoice->pdf_path && Storage::exists($invoice->pdf_path)) {
            Log::info('PDF already exists, skipping generation', [
                'invoice_id' => $invoice->id,
                'pdf_path' => $invoice->pdf_path,
            ]);

            return $invoice->pdf_path;
        }

        try {
            // 3. Générer le PDF
            $pdf = $this->pdfService->generateInvoicePdf($invoice);

            // 4. Définir le nom du fichier
            $filename = sprintf(
                'invoices/%s/%s-%s.pdf',
                $invoice->tenant_id,
                $invoice->number,
                now()->format('YmdHis')
            );

            // 5. Sauvegarder dans le storage
            Storage::put($filename, $pdf->output());

            // 6. Mettre à jour la facture
            $this->invoiceRepository->update($invoice, [
                'pdf_path' => $filename,
            ]);

            // 7. Logger l'action
            Log::info('PDF generated successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
                'pdf_path' => $filename,
                'file_size' => Storage::size($filename),
            ]);

            return $filename;

        } catch (\Exception $e) {
            Log::error('Failed to generate PDF', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to generate PDF: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Générer et télécharger le PDF directement
     */
    public function download(int $invoiceId)
    {
        $pdfPath = $this->execute($invoiceId);

        $invoice = Invoice::findOrFail($invoiceId);

        return response()->download(
            Storage::path($pdfPath),
            $invoice->number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Générer et afficher le PDF dans le navigateur
     */
    public function stream(int $invoiceId)
    {
        $invoice = Invoice::with(['client', 'items', 'user', 'tenant'])
            ->findOrFail($invoiceId);

        $pdf = $this->pdfService->generateInvoicePdf($invoice);

        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $invoice->number . '.pdf"');
    }
}
