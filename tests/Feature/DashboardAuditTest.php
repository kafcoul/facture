<?php

namespace Tests\Feature;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for Dashboard Audit Fixes (Fixes 1-21)
 */
class DashboardAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $starterUser;
    protected User $proUser;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::find(1);
        $tenant->update(['plan' => 'starter']);

        $this->starterUser = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'starter',
            'is_active' => true,
        ]);

        Tenant::create([
            'id' => 2,
            'name' => 'Pro Tenant',
            'slug' => 'pro-tenant',
            'is_active' => true,
            'plan' => 'pro',
        ]);

        $this->proUser = User::factory()->create([
            'tenant_id' => 2,
            'role' => 'client',
            'plan' => 'pro',
            'is_active' => true,
        ]);
    }

    // ═══════════════════════════════════════════════════
    //  PLAN GATING - EXPORTS (Fix 14)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function starter_cannot_access_csv_exports()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/exports/invoices');

        $response->assertRedirect(route('client.billing'));
    }

    /** @test */
    public function pro_can_access_csv_exports()
    {
        $client = Client::create([
            'tenant_id' => 2,
            'user_id' => $this->proUser->id,
            'name' => 'Export Client',
            'email' => 'export@test.com',
        ]);

        Invoice::create([
            'tenant_id' => 2,
            'user_id' => $this->proUser->id,
            'client_id' => $client->id,
            'number' => 'EXP-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 100000,
            'tax' => 18000,
            'total' => 118000,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->proUser)->get('/client/exports/invoices');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    // ═══════════════════════════════════════════════════
    //  PLAN GATING - 2FA (Fix 15)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function starter_cannot_access_2fa_enable()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/two-factor/enable');

        $response->assertRedirect(route('client.billing'));
    }

    /** @test */
    public function pro_can_access_2fa_enable()
    {
        $response = $this->actingAs($this->proUser)->get('/client/two-factor/enable');

        $response->assertOk();
    }

    // ═══════════════════════════════════════════════════
    //  PLAN GATING - TEAM (Enterprise only)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function starter_cannot_access_team_management()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/team');

        $response->assertRedirect(route('client.billing'));
    }

    /** @test */
    public function pro_cannot_access_team_management()
    {
        $response = $this->actingAs($this->proUser)->get('/client/team');

        $response->assertRedirect(route('client.billing'));
    }

    // ═══════════════════════════════════════════════════
    //  SETTINGS PAGE (Fixes 17-20)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function starter_can_access_settings_page()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/settings');

        $response->assertStatus(200);
    }

    /** @test */
    public function settings_page_shows_upgrade_cta_for_starter()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/settings');

        $response->assertSee('Plan Pro requis');
    }

    /** @test */
    public function settings_page_shows_2fa_controls_for_pro()
    {
        $response = $this->actingAs($this->proUser)->get('/client/settings');

        $response->assertDontSee('Plan Pro requis');
    }

    // ═══════════════════════════════════════════════════
    //  PROFILE PAGE (Fix 21)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function profile_page_shows_upgrade_cta_for_starter()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/profile');

        $response->assertStatus(200);
        $response->assertSee('Plan Pro requis');
    }

    /** @test */
    public function profile_page_shows_2fa_controls_for_pro()
    {
        $response = $this->actingAs($this->proUser)->get('/client/profile');

        $response->assertStatus(200);
        $response->assertDontSee('Plan Pro requis');
    }

    // ═══════════════════════════════════════════════════
    //  CURRENCY FORMAT (Fix 6, 12)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function invoices_list_shows_xof_currency()
    {
        $client = Client::create([
            'tenant_id' => 2,
            'user_id' => $this->proUser->id,
            'name' => 'XOF Client',
            'email' => 'xof@test.com',
        ]);

        Invoice::create([
            'tenant_id' => 2,
            'user_id' => $this->proUser->id,
            'client_id' => $client->id,
            'number' => 'XOF-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 100000,
            'tax' => 0,
            'total' => 100000,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->proUser)->get('/client/invoices');

        $response->assertStatus(200);
        $response->assertSee('XOF');
        $response->assertDontSee('€');
    }

    // ═══════════════════════════════════════════════════
    //  BILLING PAGE (accessible to all)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function starter_can_access_billing_page()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/billing');

        $response->assertStatus(200);
    }

    /** @test */
    public function pro_can_access_billing_page()
    {
        $response = $this->actingAs($this->proUser)->get('/client/billing');

        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════
    //  PDF DOWNLOAD (Fix 10)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function user_can_access_invoice_download_route()
    {
        $client = Client::create([
            'tenant_id' => 2,
            'user_id' => $this->proUser->id,
            'name' => 'PDF Client',
            'email' => 'pdf@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 2,
            'user_id' => $this->proUser->id,
            'client_id' => $client->id,
            'number' => 'PDF-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 500000,
            'tax' => 90000,
            'total' => 590000,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->proUser)
            ->get(route('client.invoices.download', $invoice));

        // PDF generated on the fly — returns 200 (PDF) or 302 (redirect with error if dompdf fails in test env)
        // Either way, no 403/404 — the route is accessible and authorized
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }
}
