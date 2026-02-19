<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\InvoiceTemplateService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Affiche tous les templates disponibles
     */
    public function index()
    {
        $templates = InvoiceTemplateService::getTemplates();
        
        return view('dashboard.templates.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Sélectionne un template pour l'utilisateur
     */
    public function select(Request $request, string $templateId)
    {
        /** @var User $user */
        $user = auth()->user();
        $userPlan = $user->plan ?? 'starter';

        // Vérifier si l'utilisateur peut utiliser ce template
        if (!InvoiceTemplateService::canUseTemplate($templateId, $userPlan)) {
            $template = InvoiceTemplateService::getTemplate($templateId);
            $planNames = ['starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'];
            $requiredPlan = $planNames[$template['plan']] ?? 'Pro';

            return back()->with('error', "Ce template nécessite le plan {$requiredPlan}. Mettez à niveau votre abonnement pour y accéder.");
        }

        // Mettre à jour le template de l'utilisateur
        if ($user) {
            $user->update(['invoice_template' => $templateId]);
        }

        $template = InvoiceTemplateService::getTemplate($templateId);

        return back()->with('success', "Template \"{$template['name']}\" activé avec succès !");
    }

    /**
     * Prévisualise un template avec des données exemple
     */
    public function preview(string $templateId)
    {
        $template = InvoiceTemplateService::getTemplate($templateId);
        
        if (!$template) {
            abort(404, 'Template non trouvé');
        }

        // Données exemple pour la prévisualisation
        $sampleData = [
            'invoice_number' => 'INV-2025-001',
            'date' => now()->format('d/m/Y'),
            'due_date' => now()->addDays(30)->format('d/m/Y'),
            'company' => [
                'name' => 'Votre Entreprise',
                'address' => '123 Rue du Commerce',
                'city' => 'Dakar, Sénégal',
                'phone' => '+221 77 123 45 67',
                'email' => 'contact@entreprise.com',
            ],
            'client' => [
                'name' => 'Client Exemple SARL',
                'address' => '456 Avenue des Affaires',
                'city' => 'Abidjan, Côte d\'Ivoire',
            ],
            'items' => [
                ['description' => 'Service de consultation', 'quantity' => 10, 'price' => 50000, 'total' => 500000],
                ['description' => 'Développement web', 'quantity' => 1, 'price' => 750000, 'total' => 750000],
                ['description' => 'Maintenance annuelle', 'quantity' => 1, 'price' => 200000, 'total' => 200000],
            ],
            'subtotal' => 1450000,
            'tax_rate' => 18,
            'tax_amount' => 261000,
            'total' => 1711000,
        ];

        return view('dashboard.templates.preview', [
            'template' => $template,
            'data' => $sampleData,
        ]);
    }
}
