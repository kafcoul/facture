<?php

namespace Tests\Unit\Services;

use App\Services\InvoiceTemplateService;
use Tests\TestCase;

class InvoiceTemplateServiceTest extends TestCase
{
    // ─── getTemplates ──────────────────────────────────

    /** @test */
    public function it_returns_all_eight_templates()
    {
        $templates = InvoiceTemplateService::getTemplates();

        $this->assertCount(8, $templates);
        $this->assertArrayHasKey('classic', $templates);
        $this->assertArrayHasKey('modern', $templates);
        $this->assertArrayHasKey('minimal', $templates);
        $this->assertArrayHasKey('corporate', $templates);
        $this->assertArrayHasKey('creative', $templates);
        $this->assertArrayHasKey('elegant', $templates);
        $this->assertArrayHasKey('premium', $templates);
        $this->assertArrayHasKey('african', $templates);
    }

    /** @test */
    public function each_template_has_required_fields()
    {
        $templates = InvoiceTemplateService::getTemplates();

        foreach ($templates as $id => $template) {
            $this->assertArrayHasKey('name', $template, "Template {$id} missing 'name'");
            $this->assertArrayHasKey('description', $template, "Template {$id} missing 'description'");
            $this->assertArrayHasKey('plan', $template, "Template {$id} missing 'plan'");
            $this->assertArrayHasKey('colors', $template, "Template {$id} missing 'colors'");
        }
    }

    // ─── getTemplatesForPlan ───────────────────────────

    /** @test */
    public function starter_gets_two_templates()
    {
        $templates = InvoiceTemplateService::getTemplatesForPlan('starter');

        $this->assertCount(2, $templates);
        $this->assertArrayHasKey('classic', $templates);
        $this->assertArrayHasKey('modern', $templates);
    }

    /** @test */
    public function pro_gets_five_templates()
    {
        $templates = InvoiceTemplateService::getTemplatesForPlan('pro');

        $this->assertCount(5, $templates);
        $this->assertArrayHasKey('classic', $templates);
        $this->assertArrayHasKey('modern', $templates);
        $this->assertArrayHasKey('minimal', $templates);
        $this->assertArrayHasKey('corporate', $templates);
        $this->assertArrayHasKey('creative', $templates);
    }

    /** @test */
    public function enterprise_gets_all_eight_templates()
    {
        $templates = InvoiceTemplateService::getTemplatesForPlan('enterprise');

        $this->assertCount(8, $templates);
    }

    /** @test */
    public function empty_string_plan_defaults_to_starter_templates()
    {
        $templates = InvoiceTemplateService::getTemplatesForPlan('starter');

        $this->assertCount(2, $templates);
        $this->assertArrayHasKey('classic', $templates);
        $this->assertArrayHasKey('modern', $templates);
    }

    // ─── canUseTemplate ────────────────────────────────

    /** @test */
    public function starter_can_use_classic_template()
    {
        $this->assertTrue(InvoiceTemplateService::canUseTemplate('classic', 'starter'));
    }

    /** @test */
    public function starter_cannot_use_minimal_template()
    {
        $this->assertFalse(InvoiceTemplateService::canUseTemplate('minimal', 'starter'));
    }

    /** @test */
    public function pro_can_use_minimal_template()
    {
        $this->assertTrue(InvoiceTemplateService::canUseTemplate('minimal', 'pro'));
    }

    /** @test */
    public function pro_cannot_use_african_template()
    {
        $this->assertFalse(InvoiceTemplateService::canUseTemplate('african', 'pro'));
    }

    /** @test */
    public function enterprise_can_use_african_template()
    {
        $this->assertTrue(InvoiceTemplateService::canUseTemplate('african', 'enterprise'));
    }

    /** @test */
    public function unknown_template_is_denied()
    {
        $this->assertFalse(InvoiceTemplateService::canUseTemplate('nonexistent', 'enterprise'));
    }

    // ─── getTemplate ───────────────────────────────────

    /** @test */
    public function it_returns_specific_template_details()
    {
        $template = InvoiceTemplateService::getTemplate('classic');

        $this->assertNotNull($template);
        $this->assertEquals('Classique', $template['name']);
        $this->assertEquals('starter', $template['plan']);
    }

    /** @test */
    public function it_returns_null_for_unknown_template()
    {
        $this->assertNull(InvoiceTemplateService::getTemplate('nonexistent'));
    }
}
