<?php

namespace Tests\Unit\Services;

use App\Services\PlanService;
use Tests\TestCase;

class PlanServiceTest extends TestCase
{
    // ─── getPlan ────────────────────────────────────────

    /** @test */
    public function it_returns_starter_plan_config()
    {
        $plan = PlanService::getPlan('starter');

        $this->assertNotNull($plan);
        $this->assertEquals('Starter', $plan['name']);
        $this->assertEquals(0, $plan['price']);
        $this->assertEquals('XOF', $plan['currency']);
    }

    /** @test */
    public function it_returns_pro_plan_config()
    {
        $plan = PlanService::getPlan('pro');

        $this->assertNotNull($plan);
        $this->assertEquals('Pro', $plan['name']);
        $this->assertEquals(19000, $plan['price']);
    }

    /** @test */
    public function it_returns_enterprise_plan_config()
    {
        $plan = PlanService::getPlan('enterprise');

        $this->assertNotNull($plan);
        $this->assertEquals('Enterprise', $plan['name']);
        $this->assertEquals(65000, $plan['price']);
    }

    /** @test */
    public function it_returns_null_for_unknown_plan()
    {
        $this->assertNull(PlanService::getPlan('unknown'));
        $this->assertNull(PlanService::getPlan(''));
    }

    // ─── getAllPlans ────────────────────────────────────

    /** @test */
    public function it_returns_all_three_plans()
    {
        $plans = PlanService::getAllPlans();

        $this->assertCount(3, $plans);
        $this->assertArrayHasKey('starter', $plans);
        $this->assertArrayHasKey('pro', $plans);
        $this->assertArrayHasKey('enterprise', $plans);
    }

    // ─── getLimit ───────────────────────────────────────

    /** @test */
    public function it_returns_correct_limits_for_starter()
    {
        $this->assertEquals(10, PlanService::getLimit('starter', 'invoices_per_month'));
        $this->assertEquals(5, PlanService::getLimit('starter', 'clients'));
        $this->assertEquals(20, PlanService::getLimit('starter', 'products'));
        $this->assertEquals(1, PlanService::getLimit('starter', 'team_members'));
        $this->assertEquals(100, PlanService::getLimit('starter', 'storage_mb'));
    }

    /** @test */
    public function it_returns_correct_limits_for_pro()
    {
        $this->assertEquals(100, PlanService::getLimit('pro', 'invoices_per_month'));
        $this->assertEquals(50, PlanService::getLimit('pro', 'clients'));
        $this->assertEquals(200, PlanService::getLimit('pro', 'products'));
    }

    /** @test */
    public function it_returns_minus_one_for_unlimited_enterprise()
    {
        $this->assertEquals(-1, PlanService::getLimit('enterprise', 'invoices_per_month'));
        $this->assertEquals(-1, PlanService::getLimit('enterprise', 'clients'));
        $this->assertEquals(-1, PlanService::getLimit('enterprise', 'products'));
    }

    /** @test */
    public function it_returns_zero_for_unknown_plan_limit()
    {
        $this->assertEquals(0, PlanService::getLimit('unknown', 'clients'));
    }

    /** @test */
    public function it_returns_zero_for_unknown_resource()
    {
        $this->assertEquals(0, PlanService::getLimit('starter', 'nonexistent'));
    }

    // ─── hasFeature ─────────────────────────────────────

    /** @test */
    public function starter_has_basic_features()
    {
        $this->assertTrue(PlanService::hasFeature('starter', 'basic_invoicing'));
        $this->assertTrue(PlanService::hasFeature('starter', 'pdf_export'));
        $this->assertTrue(PlanService::hasFeature('starter', 'email_sending'));
    }

    /** @test */
    public function starter_lacks_advanced_features()
    {
        $this->assertFalse(PlanService::hasFeature('starter', 'client_management'));
        $this->assertFalse(PlanService::hasFeature('starter', 'analytics'));
        $this->assertFalse(PlanService::hasFeature('starter', 'team_management'));
        $this->assertFalse(PlanService::hasFeature('starter', 'api_access'));
        $this->assertFalse(PlanService::hasFeature('starter', 'white_label'));
        $this->assertFalse(PlanService::hasFeature('starter', 'two_factor_auth'));
    }

    /** @test */
    public function pro_has_mid_tier_features()
    {
        $this->assertTrue(PlanService::hasFeature('pro', 'client_management'));
        $this->assertTrue(PlanService::hasFeature('pro', 'product_catalog'));
        $this->assertTrue(PlanService::hasFeature('pro', 'analytics'));
        $this->assertTrue(PlanService::hasFeature('pro', 'custom_templates'));
        $this->assertTrue(PlanService::hasFeature('pro', 'two_factor_auth'));
        $this->assertTrue(PlanService::hasFeature('pro', 'multi_currency'));
        $this->assertTrue(PlanService::hasFeature('pro', 'recurring_invoices'));
        $this->assertTrue(PlanService::hasFeature('pro', 'payment_reminders'));
    }

    /** @test */
    public function pro_lacks_enterprise_features()
    {
        $this->assertFalse(PlanService::hasFeature('pro', 'team_management'));
        $this->assertFalse(PlanService::hasFeature('pro', 'api_access'));
        $this->assertFalse(PlanService::hasFeature('pro', 'white_label'));
    }

    /** @test */
    public function enterprise_has_all_features()
    {
        $features = PlanService::getPlan('enterprise')['features'];
        foreach ($features as $feature => $enabled) {
            $this->assertTrue($enabled, "Enterprise should have feature: {$feature}");
        }
    }

    /** @test */
    public function has_feature_returns_false_for_unknown_plan()
    {
        $this->assertFalse(PlanService::hasFeature('unknown', 'basic_invoicing'));
    }

    /** @test */
    public function has_feature_returns_false_for_unknown_feature()
    {
        $this->assertFalse(PlanService::hasFeature('pro', 'nonexistent_feature'));
    }

    // ─── isWithinLimit ──────────────────────────────────

    /** @test */
    public function starter_is_within_limit_with_low_usage()
    {
        $this->assertTrue(PlanService::isWithinLimit('starter', 'invoices_per_month', 0));
        $this->assertTrue(PlanService::isWithinLimit('starter', 'invoices_per_month', 5));
        $this->assertTrue(PlanService::isWithinLimit('starter', 'invoices_per_month', 9));
    }

    /** @test */
    public function starter_exceeds_limit_at_max()
    {
        $this->assertFalse(PlanService::isWithinLimit('starter', 'invoices_per_month', 10));
        $this->assertFalse(PlanService::isWithinLimit('starter', 'invoices_per_month', 15));
    }

    /** @test */
    public function enterprise_always_within_limit_for_unlimited_resources()
    {
        $this->assertTrue(PlanService::isWithinLimit('enterprise', 'invoices_per_month', 0));
        $this->assertTrue(PlanService::isWithinLimit('enterprise', 'invoices_per_month', 10000));
        $this->assertTrue(PlanService::isWithinLimit('enterprise', 'clients', 999999));
    }

    // ─── isUpgrade / isDowngrade ────────────────────────

    /** @test */
    public function starter_to_pro_is_upgrade()
    {
        $this->assertTrue(PlanService::isUpgrade('starter', 'pro'));
    }

    /** @test */
    public function starter_to_enterprise_is_upgrade()
    {
        $this->assertTrue(PlanService::isUpgrade('starter', 'enterprise'));
    }

    /** @test */
    public function pro_to_enterprise_is_upgrade()
    {
        $this->assertTrue(PlanService::isUpgrade('pro', 'enterprise'));
    }

    /** @test */
    public function pro_to_starter_is_not_upgrade()
    {
        $this->assertFalse(PlanService::isUpgrade('pro', 'starter'));
    }

    /** @test */
    public function same_plan_is_not_upgrade()
    {
        $this->assertFalse(PlanService::isUpgrade('pro', 'pro'));
    }

    /** @test */
    public function enterprise_to_pro_is_downgrade()
    {
        $this->assertTrue(PlanService::isDowngrade('enterprise', 'pro'));
    }

    /** @test */
    public function pro_to_starter_is_downgrade()
    {
        $this->assertTrue(PlanService::isDowngrade('pro', 'starter'));
    }

    /** @test */
    public function starter_to_pro_is_not_downgrade()
    {
        $this->assertFalse(PlanService::isDowngrade('starter', 'pro'));
    }

    /** @test */
    public function same_plan_is_not_downgrade()
    {
        $this->assertFalse(PlanService::isDowngrade('pro', 'pro'));
    }

    // ─── getPrice / getFormattedPrice ───────────────────

    /** @test */
    public function it_returns_correct_prices()
    {
        $this->assertEquals(0, PlanService::getPrice('starter'));
        $this->assertEquals(19000, PlanService::getPrice('pro'));
        $this->assertEquals(65000, PlanService::getPrice('enterprise'));
    }

    /** @test */
    public function it_returns_zero_for_unknown_plan_price()
    {
        $this->assertEquals(0, PlanService::getPrice('unknown'));
    }

    /** @test */
    public function it_formats_starter_as_gratuit()
    {
        $this->assertEquals('Gratuit', PlanService::getFormattedPrice('starter'));
    }

    /** @test */
    public function it_formats_pro_price_with_xof()
    {
        $formatted = PlanService::getFormattedPrice('pro');
        $this->assertStringContainsString('19', $formatted);
        $this->assertStringContainsString('XOF', $formatted);
    }

    /** @test */
    public function it_formats_enterprise_price_with_xof()
    {
        $formatted = PlanService::getFormattedPrice('enterprise');
        $this->assertStringContainsString('65', $formatted);
        $this->assertStringContainsString('XOF', $formatted);
    }

    // ─── isTrialExpired / trialDaysRemaining ────────────

    /** @test */
    public function trial_is_not_expired_when_no_trial_date()
    {
        $entity = new \stdClass();
        $entity->trial_ends_at = null;

        $this->assertFalse(PlanService::isTrialExpired($entity));
    }

    /** @test */
    public function trial_is_expired_when_date_is_past()
    {
        $entity = new \stdClass();
        $entity->trial_ends_at = now()->subDay();

        $this->assertTrue(PlanService::isTrialExpired($entity));
    }

    /** @test */
    public function trial_is_not_expired_when_date_is_future()
    {
        $entity = new \stdClass();
        $entity->trial_ends_at = now()->addDays(10);

        $this->assertFalse(PlanService::isTrialExpired($entity));
    }

    /** @test */
    public function trial_days_remaining_returns_zero_when_no_trial()
    {
        $entity = new \stdClass();
        $entity->trial_ends_at = null;

        $this->assertEquals(0, PlanService::trialDaysRemaining($entity));
    }

    /** @test */
    public function trial_days_remaining_returns_zero_when_expired()
    {
        $entity = new \stdClass();
        $entity->trial_ends_at = now()->subDays(5);

        $this->assertEquals(0, PlanService::trialDaysRemaining($entity));
    }

    /** @test */
    public function trial_days_remaining_returns_correct_days()
    {
        $entity = new \stdClass();
        $entity->trial_ends_at = now()->addDays(15);

        $remaining = PlanService::trialDaysRemaining($entity);
        $this->assertGreaterThanOrEqual(14, $remaining);
        $this->assertLessThanOrEqual(15, $remaining);
    }

    // ─── Plan structure integrity ───────────────────────

    /** @test */
    public function all_plans_have_required_keys()
    {
        $requiredKeys = ['name', 'price', 'currency', 'billing_period', 'description', 'limits', 'features'];

        foreach (PlanService::PLANS as $planKey => $plan) {
            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $plan, "Plan '{$planKey}' missing key '{$key}'");
            }
        }
    }

    /** @test */
    public function all_plans_have_required_limits()
    {
        $requiredLimits = ['invoices_per_month', 'clients', 'products', 'team_members', 'storage_mb', 'payment_gateways', 'templates'];

        foreach (PlanService::PLANS as $planKey => $plan) {
            foreach ($requiredLimits as $limit) {
                $this->assertArrayHasKey($limit, $plan['limits'], "Plan '{$planKey}' missing limit '{$limit}'");
            }
        }
    }

    /** @test */
    public function all_plans_have_required_features()
    {
        $requiredFeatures = [
            'basic_invoicing', 'pdf_export', 'email_sending', 'client_management',
            'product_catalog', 'analytics', 'custom_templates', 'team_management',
            'api_access', 'priority_support', 'two_factor_auth', 'multi_currency',
            'recurring_invoices', 'payment_reminders', 'white_label',
        ];

        foreach (PlanService::PLANS as $planKey => $plan) {
            foreach ($requiredFeatures as $feature) {
                $this->assertArrayHasKey($feature, $plan['features'], "Plan '{$planKey}' missing feature '{$feature}'");
            }
        }
    }

    /** @test */
    public function plan_hierarchy_is_consistent()
    {
        $hierarchy = PlanService::PLAN_HIERARCHY;

        $this->assertLessThan($hierarchy['enterprise'], $hierarchy['pro']);
        $this->assertLessThan($hierarchy['pro'], $hierarchy['starter']);
        $this->assertEquals(0, $hierarchy['starter']);
    }

    /** @test */
    public function higher_plan_has_equal_or_more_limits()
    {
        $plans = ['starter', 'pro', 'enterprise'];
        $numericLimits = ['invoices_per_month', 'clients', 'products', 'team_members', 'storage_mb'];

        for ($i = 0; $i < count($plans) - 1; $i++) {
            $current = $plans[$i];
            $next = $plans[$i + 1];

            foreach ($numericLimits as $limit) {
                $currentLimit = PlanService::getLimit($current, $limit);
                $nextLimit = PlanService::getLimit($next, $limit);

                // -1 means unlimited, so it's always >= any positive
                if ($nextLimit === -1) {
                    $this->assertTrue(true);
                } else {
                    $this->assertGreaterThanOrEqual(
                        $currentLimit,
                        $nextLimit,
                        "{$next}.{$limit} ({$nextLimit}) should be >= {$current}.{$limit} ({$currentLimit})"
                    );
                }
            }
        }
    }
}
