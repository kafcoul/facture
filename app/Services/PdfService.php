<?php

namespace App\Services;

use App\Domain\Invoice\Models\Invoice;
use App\Services\InvoiceTemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * IDs de templates valides (correspondent aux fichiers pdf/templates/*.blade.php)
     * Alignés avec InvoiceTemplateService
     */
    private const VALID_TEMPLATES = [
        'classic', 'modern',                          // starter
        'minimal', 'corporate', 'creative',           // pro
        'elegant', 'premium', 'african',              // enterprise
    ];

    /**
     * Template par défaut pour chaque plan
     */
    private const DEFAULT_TEMPLATE = [
        'starter' => 'classic',
        'pro' => 'minimal',
        'enterprise' => 'elegant',
    ];

    /**
     * Générer le PDF d'une facture
     * 
     * @param Invoice $invoice La facture avec relations chargées (client, items, user, tenant)
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateInvoicePdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        // Vérifier que les relations nécessaires sont chargées
        if (!$invoice->relationLoaded('client')) {
            $invoice->load('client');
        }
        if (!$invoice->relationLoaded('items')) {
            $invoice->load('items.product');
        }
        if (!$invoice->relationLoaded('user')) {
            $invoice->load('user');
        }

        // Déterminer le template à utiliser
        $template = $this->getTemplateForInvoice($invoice);

        // Générer le PDF avec la vue
        return Pdf::loadView("pdf.templates.{$template}", [
            'invoice' => $invoice,
            'client' => $invoice->client,
            'items' => $invoice->items,
            'user' => $invoice->user,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);
    }

    /**
     * Déterminer le template à utiliser pour une facture
     * Utilise InvoiceTemplateService pour la validation des plans
     */
    private function getTemplateForInvoice(Invoice $invoice): string
    {
        $user = $invoice->user;
        $plan = $user->plan ?? 'starter';
        $selectedTemplate = $user->invoice_template ?? null;

        // Si un template est sélectionné, vérifier qu'il est valide et autorisé
        if ($selectedTemplate && in_array($selectedTemplate, self::VALID_TEMPLATES)) {
            // Vérifier via InvoiceTemplateService que le plan autorise ce template
            if (InvoiceTemplateService::canUseTemplate($selectedTemplate, $plan)) {
                // Vérifier que le fichier Blade existe
                if (view()->exists("pdf.templates.{$selectedTemplate}")) {
                    return $selectedTemplate;
                }
            }
        }

        // Fallback sur le template par défaut du plan
        return $this->getDefaultTemplateForPlan($plan);
    }

    /**
     * Obtenir le template par défaut pour un plan
     */
    private function getDefaultTemplateForPlan(string $plan): string
    {
        return self::DEFAULT_TEMPLATE[$plan] ?? self::DEFAULT_TEMPLATE['starter'];
    }

    /**
     * Obtenir les templates disponibles pour un plan
     * Délègue à InvoiceTemplateService (source unique de vérité)
     */
    public static function getAvailableTemplates(string $plan): array
    {
        return InvoiceTemplateService::getTemplatesForPlan($plan);
    }

    /**
     * Legacy static method pour compatibilité
     */
    public static function make(Invoice $invoice): string
    {
        $service = new self();
        $pdf = $service->generateInvoicePdf($invoice);
        $path = "invoices/{$invoice->number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());
        return $path;
    }
}
