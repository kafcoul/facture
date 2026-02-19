<?php

namespace Tests\Feature;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Tenant\Models\Tenant;
use App\Models\Product;
use App\Models\User;
use App\Services\PlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::find(1);
        $this->tenant->update([
            'plan' => 'starter',
            'trial_ends_at' => now()->addDays(30),
        ]);

        $this->user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'starter',
            'trial_ends_at' => now()->addDays(30),
        ]);
    }

    // ─── Index ──────────────────────────────────────────

    /** @test */
    public function billing_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get('/client/billing');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.billing.index');
    }

    /** @test */
    public function billing_page_shows_current_plan()
    {
        $response = $this->actingAs($this->user)->get('/client/billing');

        $response->assertStatus(200);
        $response->assertViewHas('currentPlan', 'starter');
        $response->assertViewHas('plans');
    }

    /** @test */
    public function billing_page_shows_usage_data()
    {
        // Créer quelques factures
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client Test',
            'email' => 'client@test.com',
        ]);

        Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-001', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get('/client/billing');

        $response->assertStatus(200);
        $response->assertViewHas('usage', function ($usage) {
            return $usage['invoices_this_month'] === 1
                && $usage['clients'] === 1;
        });
    }

    /** @test */
    public function billing_page_shows_trial_info()
    {
        $response = $this->actingAs($this->user)->get('/client/billing');

        $response->assertStatus(200);
        $response->assertViewHas('isOnTrial', true);
        $response->assertViewHas('trialDaysRemaining');
    }

    /** @test */
    public function billing_page_shows_all_plans()
    {
        $response = $this->actingAs($this->user)->get('/client/billing');

        $response->assertViewHas('plans', function ($plans) {
            return count($plans) === 3
                && isset($plans['starter'])
                && isset($plans['pro'])
                && isset($plans['enterprise']);
        });
    }

    // ─── Upgrade ────────────────────────────────────────

    /** @test */
    public function user_can_upgrade_from_starter_to_pro()
    {
        $response = $this->actingAs($this->user)->post('/client/billing/upgrade', [
            'plan' => 'pro',
        ]);

        $response->assertRedirect(route('client.billing'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('pro', $this->user->plan);

        $this->tenant->refresh();
        $this->assertEquals('pro', $this->tenant->plan);
    }

    /** @test */
    public function user_can_upgrade_from_starter_to_enterprise()
    {
        $response = $this->actingAs($this->user)->post('/client/billing/upgrade', [
            'plan' => 'enterprise',
        ]);

        $response->assertRedirect(route('client.billing'));
        $this->user->refresh();
        $this->assertEquals('enterprise', $this->user->plan);
    }

    /** @test */
    public function user_can_upgrade_from_pro_to_enterprise()
    {
        $this->user->update(['plan' => 'pro']);
        $this->tenant->update(['plan' => 'pro']);

        $response = $this->actingAs($this->user)->post('/client/billing/upgrade', [
            'plan' => 'enterprise',
        ]);

        $response->assertRedirect(route('client.billing'));
        $this->user->refresh();
        $this->assertEquals('enterprise', $this->user->plan);
    }

    /** @test */
    public function upgrade_rejects_invalid_plan()
    {
        $response = $this->actingAs($this->user)->post('/client/billing/upgrade', [
            'plan' => 'invalid_plan',
        ]);

        $response->assertSessionHasErrors('plan');
    }

    /** @test */
    public function upgrade_rejects_downgrade_attempt()
    {
        $this->user->update(['plan' => 'enterprise']);
        $this->tenant->update(['plan' => 'enterprise']);

        $response = $this->actingAs($this->user)->post('/client/billing/upgrade', [
            'plan' => 'pro',
        ]);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function upgrade_rejects_same_plan()
    {
        $this->user->update(['plan' => 'pro']);
        $this->tenant->update(['plan' => 'pro']);

        $response = $this->actingAs($this->user)->post('/client/billing/upgrade', [
            'plan' => 'pro',
        ]);

        // Not a valid upgrade
        $response->assertSessionHas('error');
    }

    // ─── Downgrade ──────────────────────────────────────

    /** @test */
    public function user_can_downgrade_from_pro_to_starter()
    {
        $this->user->update(['plan' => 'pro']);
        $this->tenant->update(['plan' => 'pro']);

        $response = $this->actingAs($this->user)->post('/client/billing/downgrade', [
            'plan' => 'starter',
        ]);

        $response->assertRedirect(route('client.billing'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('starter', $this->user->plan);
    }

    /** @test */
    public function user_can_downgrade_from_enterprise_to_pro()
    {
        $this->user->update(['plan' => 'enterprise']);
        $this->tenant->update(['plan' => 'enterprise']);

        $response = $this->actingAs($this->user)->post('/client/billing/downgrade', [
            'plan' => 'pro',
        ]);

        $response->assertRedirect(route('client.billing'));
        $this->user->refresh();
        $this->assertEquals('pro', $this->user->plan);
    }

    /** @test */
    public function downgrade_rejects_upgrade_attempt()
    {
        $response = $this->actingAs($this->user)->post('/client/billing/downgrade', [
            'plan' => 'pro',
        ]);

        // starter→pro is upgrade, not downgrade
        $response->assertSessionHas('error');
    }

    /** @test */
    public function downgrade_rejects_invalid_plan()
    {
        $this->user->update(['plan' => 'pro']);

        $response = $this->actingAs($this->user)->post('/client/billing/downgrade', [
            'plan' => 'enterprise',
        ]);

        $response->assertSessionHasErrors('plan');
    }

    /** @test */
    public function downgrade_blocked_when_exceeding_new_plan_limits()
    {
        $this->user->update(['plan' => 'pro']);
        $this->tenant->update(['plan' => 'pro']);

        // Créer 6 clients (starter limit = 5)
        for ($i = 1; $i <= 6; $i++) {
            Client::create([
                'tenant_id' => 1,
                'user_id' => $this->user->id,
                'name' => "Client {$i}",
                'email' => "client{$i}@test.com",
            ]);
        }

        $response = $this->actingAs($this->user)->post('/client/billing/downgrade', [
            'plan' => 'starter',
        ]);

        $response->assertSessionHas('error');

        // Plan should NOT have changed
        $this->user->refresh();
        $this->assertEquals('pro', $this->user->plan);
    }

    // ─── Cancel ─────────────────────────────────────────

    /** @test */
    public function user_can_cancel_subscription()
    {
        $this->user->update(['plan' => 'pro']);
        $this->tenant->update(['plan' => 'pro']);

        $response = $this->actingAs($this->user)->post('/client/billing/cancel');

        $response->assertRedirect(route('client.billing'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('starter', $this->user->plan);

        $this->tenant->refresh();
        $this->assertEquals('starter', $this->tenant->plan);
        $this->assertNull($this->tenant->trial_ends_at);
    }

    /** @test */
    public function cancel_fails_for_already_starter()
    {
        $response = $this->actingAs($this->user)->post('/client/billing/cancel');

        $response->assertSessionHas('error');
    }

    // ─── Auth guard ─────────────────────────────────────

    /** @test */
    public function billing_requires_authentication()
    {
        $response = $this->get('/client/billing');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function upgrade_requires_authentication()
    {
        $response = $this->post('/client/billing/upgrade', ['plan' => 'pro']);
        $response->assertRedirect('/login');
    }
}
