<?php

namespace App\Services;

use App\Domain\Invoice\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Templates disponibles par plan
     */
    private const PLAN_TEMPLATES = [
        'starter' => ['starter'],
        'pro' => ['starter', 'pro', 'pro-minimal', 'pro-bold'],
        'enterprise' => ['starter', 'pro', 'pro-minimal', 'pro-bold', 'enterprise', 'enterprise-dark', 'enterprise-minimal'],
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
     */
    private function getTemplateForInvoice(Invoice $invoice): string
    {
        $user = $invoice->user;
        $plan = $user->plan ?? 'starter';
        $selectedTemplate = $user->invoice_template ?? 'starter';

        // Vérifier que le template est autorisé pour le plan
        $allowedTemplates = self::PLAN_TEMPLATES[$plan] ?? self::PLAN_TEMPLATES['starter'];

        if (in_array($selectedTemplate, $allowedTemplates)) {
            // Vérifier que le fichier existe
            if (view()->exists("pdf.templates.{$selectedTemplate}")) {
                return $selectedTemplate;
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
        return match($plan) {
            'enterprise' => 'enterprise',
            'pro' => 'pro',
            default => 'starter',
        };
    }

    /**
     * Obtenir les templates disponibles pour un plan
     */
    public static function getAvailableTemplates(string $plan): array
    {
        $templates = [
            'starter' => [
                'id' => 'starter',
                'name' => 'Classique',
                'description' => 'Design simple et professionnel',
                'preview' => '/images/templates/starter.png',
            ],
            'pro' => [
                'id' => 'pro',
                'name' => 'Pro Moderne',
                'description' => 'Design moderne avec dégradés et QR code paiement',
                'preview' => '/images/templates/pro.png',
            ],
            'pro-minimal' => [
                'id' => 'pro-minimal',
                'name' => 'Pro Minimal',
                'description' => 'Design épuré et minimaliste',
                'preview' => '/images/templates/pro-minimal.png',
            ],
            'pro-bold' => [
                'id' => 'pro-bold',
                'name' => 'Pro Bold',
                'description' => 'Design audacieux avec couleurs vives',
                'preview' => '/images/templates/pro-bold.png',
            ],
            'enterprise' => [
                'id' => 'enterprise',
                'name' => 'Enterprise Premium',
                'description' => 'Design premium avec QR code et montant en lettres',
                'preview' => '/images/templates/enterprise.png',
            ],
            'enterprise-dark' => [
                'id' => 'enterprise-dark',
                'name' => 'Enterprise Dark',
                'description' => 'Design sombre et élégant',
                'preview' => '/images/templates/enterprise-dark.png',
            ],
            'enterprise-minimal' => [
                'id' => 'enterprise-minimal',
                'name' => 'Enterprise Minimal',
                'description' => 'Design minimaliste haut de gamme',
                'preview' => '/images/templates/enterprise-minimal.png',
            ],
        ];

        $allowedTemplateIds = self::PLAN_TEMPLATES[$plan] ?? self::PLAN_TEMPLATES['starter'];

        return array_filter($templates, fn($t) => in_array($t['id'], $allowedTemplateIds));
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
