<?php

namespace App\Services;

/**
 * Service centralisé pour la gestion des plans et limites
 */
class PlanService
{
    /**
     * Définition des plans avec leurs fonctionnalités et limites
     */
    public const PLANS = [
        'starter' => [
            'name' => 'Starter',
            'price' => 0,
            'currency' => 'XOF',
            'billing_period' => 'month',
            'description' => 'Pour démarrer gratuitement',
            'limits' => [
                'invoices_per_month' => 10,
                'clients' => 5,
                'products' => 20,
                'team_members' => 1,
                'templates' => ['modern'],
                'payment_gateways' => ['stripe'],
                'storage_mb' => 100,
            ],
            'features' => [
                'basic_invoicing' => true,
                'pdf_export' => true,
                'email_sending' => true,
                'client_management' => false,
                'product_catalog' => false,
                'analytics' => false,
                'custom_templates' => false,
                'team_management' => false,
                'api_access' => false,
                'priority_support' => false,
                'two_factor_auth' => false,
                'multi_currency' => false,
                'recurring_invoices' => false,
                'payment_reminders' => false,
                'white_label' => false,
            ],
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 19000,
            'currency' => 'XOF',
            'billing_period' => 'month',
            'description' => 'Pour les professionnels',
            'limits' => [
                'invoices_per_month' => 100,
                'clients' => 50,
                'products' => 200,
                'team_members' => 1,
                'templates' => ['modern', 'classic', 'minimal', 'corporate'],
                'payment_gateways' => ['stripe', 'paystack', 'wave', 'orange_money'],
                'storage_mb' => 1000,
            ],
            'features' => [
                'basic_invoicing' => true,
                'pdf_export' => true,
                'email_sending' => true,
                'client_management' => true,
                'product_catalog' => true,
                'analytics' => true,
                'custom_templates' => true,
                'team_management' => false,
                'api_access' => false,
                'priority_support' => true,
                'two_factor_auth' => true,
                'multi_currency' => true,
                'recurring_invoices' => true,
                'payment_reminders' => true,
                'white_label' => false,
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => 65000,
            'currency' => 'XOF',
            'billing_period' => 'month',
            'description' => 'Pour les entreprises',
            'limits' => [
                'invoices_per_month' => -1, // illimité
                'clients' => -1,
                'products' => -1,
                'team_members' => 20,
                'templates' => ['modern', 'classic', 'minimal', 'corporate', 'premium', 'luxury'],
                'payment_gateways' => ['stripe', 'paystack', 'flutterwave', 'wave', 'orange_money', 'mtn_momo', 'mpesa', 'fedapay', 'kkiapay', 'cinetpay'],
                'storage_mb' => 10000,
            ],
            'features' => [
                'basic_invoicing' => true,
                'pdf_export' => true,
                'email_sending' => true,
                'client_management' => true,
                'product_catalog' => true,
                'analytics' => true,
                'custom_templates' => true,
                'team_management' => true,
                'api_access' => true,
                'priority_support' => true,
                'two_factor_auth' => true,
                'multi_currency' => true,
                'recurring_invoices' => true,
                'payment_reminders' => true,
                'white_label' => true,
            ],
        ],
    ];

    /**
     * Hiérarchie des plans (pour upgrade/downgrade)
     */
    public const PLAN_HIERARCHY = [
        'starter' => 0,
        'pro' => 1,
        'enterprise' => 2,
    ];

    /**
     * Obtenir la config d'un plan
     */
    public static function getPlan(string $plan): ?array
    {
        return self::PLANS[$plan] ?? null;
    }

    /**
     * Tous les plans
     */
    public static function getAllPlans(): array
    {
        return self::PLANS;
    }

    /**
     * Obtenir la limite d'un plan pour une ressource
     */
    public static function getLimit(string $plan, string $resource): int
    {
        $planConfig = self::getPlan($plan);
        if (!$planConfig) return 0;

        $limit = $planConfig['limits'][$resource] ?? 0;
        return $limit; // -1 = illimité
    }

    /**
     * Vérifier si un plan a une fonctionnalité
     */
    public static function hasFeature(string $plan, string $feature): bool
    {
        $planConfig = self::getPlan($plan);
        if (!$planConfig) return false;

        return $planConfig['features'][$feature] ?? false;
    }

    /**
     * Vérifier si un utilisateur est dans les limites de son plan
     */
    public static function isWithinLimit(string $plan, string $resource, int $currentCount): bool
    {
        $limit = self::getLimit($plan, $resource);
        if ($limit === -1) return true; // illimité
        return $currentCount < $limit;
    }

    /**
     * Vérifier si c'est un upgrade
     */
    public static function isUpgrade(string $currentPlan, string $newPlan): bool
    {
        $currentLevel = self::PLAN_HIERARCHY[$currentPlan] ?? 0;
        $newLevel = self::PLAN_HIERARCHY[$newPlan] ?? 0;
        return $newLevel > $currentLevel;
    }

    /**
     * Vérifier si c'est un downgrade
     */
    public static function isDowngrade(string $currentPlan, string $newPlan): bool
    {
        $currentLevel = self::PLAN_HIERARCHY[$currentPlan] ?? 0;
        $newLevel = self::PLAN_HIERARCHY[$newPlan] ?? 0;
        return $newLevel < $currentLevel;
    }

    /**
     * Obtenir le prix d'un plan
     */
    public static function getPrice(string $plan): int
    {
        return self::PLANS[$plan]['price'] ?? 0;
    }

    /**
     * Obtenir le prix formaté
     */
    public static function getFormattedPrice(string $plan): string
    {
        $price = self::getPrice($plan);
        if ($price === 0) return 'Gratuit';
        return number_format($price, 0, ',', ' ') . ' XOF/mois';
    }

    /**
     * Vérifier si le trial est expiré pour un user/tenant
     */
    public static function isTrialExpired($entity): bool
    {
        if (!$entity->trial_ends_at) return false;
        return $entity->trial_ends_at->isPast();
    }

    /**
     * Jours restants dans le trial
     */
    public static function trialDaysRemaining($entity): int
    {
        if (!$entity->trial_ends_at) return 0;
        if ($entity->trial_ends_at->isPast()) return 0;
        return (int) now()->diffInDays($entity->trial_ends_at);
    }
}
