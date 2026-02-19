<?php

namespace App\Services;

class InvoiceTemplateService
{
    /**
     * Tous les templates disponibles avec leurs plans requis
     */
    public static function getTemplates(): array
    {
        return [
            'classic' => [
                'id' => 'classic',
                'name' => 'Classique',
                'description' => 'Design simple et professionnel',
                'plan' => 'starter',
                'preview' => '/images/templates/classic.png',
                'colors' => ['primary' => '#3B82F6', 'secondary' => '#1E40AF'],
            ],
            'modern' => [
                'id' => 'modern',
                'name' => 'Moderne',
                'description' => 'Style épuré avec accents colorés',
                'plan' => 'starter',
                'preview' => '/images/templates/modern.png',
                'colors' => ['primary' => '#8B5CF6', 'secondary' => '#6D28D9'],
            ],
            'minimal' => [
                'id' => 'minimal',
                'name' => 'Minimaliste',
                'description' => 'Ultra simple, focus sur le contenu',
                'plan' => 'pro',
                'preview' => '/images/templates/minimal.png',
                'colors' => ['primary' => '#111827', 'secondary' => '#374151'],
            ],
            'corporate' => [
                'id' => 'corporate',
                'name' => 'Corporate',
                'description' => 'Idéal pour les grandes entreprises',
                'plan' => 'pro',
                'preview' => '/images/templates/corporate.png',
                'colors' => ['primary' => '#0F766E', 'secondary' => '#115E59'],
            ],
            'creative' => [
                'id' => 'creative',
                'name' => 'Créatif',
                'description' => 'Design audacieux et coloré',
                'plan' => 'pro',
                'preview' => '/images/templates/creative.png',
                'colors' => ['primary' => '#EC4899', 'secondary' => '#BE185D'],
            ],
            'elegant' => [
                'id' => 'elegant',
                'name' => 'Élégant',
                'description' => 'Sophistiqué avec touches dorées',
                'plan' => 'enterprise',
                'preview' => '/images/templates/elegant.png',
                'colors' => ['primary' => '#B45309', 'secondary' => '#92400E'],
            ],
            'premium' => [
                'id' => 'premium',
                'name' => 'Premium',
                'description' => 'Design luxueux noir et or',
                'plan' => 'enterprise',
                'preview' => '/images/templates/premium.png',
                'colors' => ['primary' => '#1F2937', 'secondary' => '#D97706'],
            ],
            'african' => [
                'id' => 'african',
                'name' => 'Africain',
                'description' => 'Motifs africains authentiques',
                'plan' => 'enterprise',
                'preview' => '/images/templates/african.png',
                'colors' => ['primary' => '#DC2626', 'secondary' => '#16A34A'],
            ],
        ];
    }

    /**
     * Templates disponibles pour un plan donné
     */
    public static function getTemplatesForPlan(string $plan): array
    {
        $allTemplates = self::getTemplates();
        $planHierarchy = ['starter' => 1, 'pro' => 2, 'enterprise' => 3];
        $userPlanLevel = $planHierarchy[$plan] ?? 1;

        return array_filter($allTemplates, function ($template) use ($planHierarchy, $userPlanLevel) {
            $templatePlanLevel = $planHierarchy[$template['plan']] ?? 1;
            return $templatePlanLevel <= $userPlanLevel;
        });
    }

    /**
     * Vérifie si un utilisateur peut utiliser un template
     */
    public static function canUseTemplate(string $templateId, string $userPlan): bool
    {
        $templates = self::getTemplates();
        if (!isset($templates[$templateId])) {
            return false;
        }

        $planHierarchy = ['starter' => 1, 'pro' => 2, 'enterprise' => 3];
        $userPlanLevel = $planHierarchy[$userPlan] ?? 1;
        $templatePlanLevel = $planHierarchy[$templates[$templateId]['plan']] ?? 1;

        return $userPlanLevel >= $templatePlanLevel;
    }

    /**
     * Obtenir un template par ID
     */
    public static function getTemplate(string $templateId): ?array
    {
        $templates = self::getTemplates();
        return $templates[$templateId] ?? null;
    }
}
