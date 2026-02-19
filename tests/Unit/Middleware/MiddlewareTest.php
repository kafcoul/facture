<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\CheckPlan;
use App\Http\Middleware\CheckTrialExpired;
use App\Models\User;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════
    //  CheckPlan Middleware
    // ═══════════════════════════════════════════════════

    /** @test */
    public function check_plan_allows_user_with_matching_plan()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'pro',
        ]);

        $this->actingAs($user);

        $response = $this->get('/client/clients');

        // Si le plan est autorisé, pas de redirect vers billing
        // (Peut retourner 200 ou 500 selon le controller, mais pas redirect vers billing)
        $this->assertNotEquals(302, $response->getStatusCode(), 'Should not redirect when plan matches');
    }

    /** @test */
    public function check_plan_redirects_starter_from_pro_routes()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'starter',
        ]);

        $this->actingAs($user);

        $response = $this->get('/client/clients');

        $response->assertRedirect(route('client.billing'));
    }

    /** @test */
    public function check_plan_returns_403_json_for_api_requests()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'starter',
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/client/clients');

        $response->assertStatus(403);
        $response->assertJsonStructure(['error', 'required_plans', 'current_plan', 'upgrade_url']);
    }

    /** @test */
    public function check_plan_allows_enterprise_user_on_enterprise_routes()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'enterprise',
        ]);

        $this->actingAs($user);

        // Enterprise route - ne devrait PAS redirect vers billing
        $response = $this->get('/client/team');
        $this->assertNotEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function check_plan_blocks_pro_from_enterprise_routes()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'pro',
        ]);

        $this->actingAs($user);

        $response = $this->get('/client/team');

        $response->assertRedirect(route('client.billing'));
    }

    // ═══════════════════════════════════════════════════
    //  CheckTrialExpired Middleware
    // ═══════════════════════════════════════════════════

    /** @test */
    public function trial_middleware_passes_for_user_without_trial()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'pro',
            'trial_ends_at' => null,
        ]);

        $this->actingAs($user);

        $request = Request::create('/client', 'GET');
        $request->setUserResolver(fn() => $user);

        $middleware = new CheckTrialExpired();
        $response = $middleware->handle($request, fn() => new Response('OK'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function trial_middleware_passes_for_super_admin()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'super_admin',
            'plan' => 'pro',
            'trial_ends_at' => now()->subDays(5), // expired
        ]);

        $request = Request::create('/client', 'GET');
        $request->setUserResolver(fn() => $user);

        $middleware = new CheckTrialExpired();
        $response = $middleware->handle($request, fn() => new Response('OK'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function trial_middleware_passes_for_starter_plan()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'starter',
            'trial_ends_at' => now()->subDays(5),
        ]);

        $request = Request::create('/client', 'GET');
        $request->setUserResolver(fn() => $user);

        $middleware = new CheckTrialExpired();
        $response = $middleware->handle($request, fn() => new Response('OK'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function trial_middleware_passes_when_trial_not_expired()
    {
        $tenant = Tenant::find(1);
        $tenant->update(['trial_ends_at' => now()->addDays(10)]);

        $user = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'pro',
            'trial_ends_at' => now()->addDays(10),
        ]);

        $request = Request::create('/client', 'GET');
        $request->setUserResolver(fn() => $user);

        $middleware = new CheckTrialExpired();
        $response = $middleware->handle($request, fn() => new Response('OK'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function trial_middleware_returns_402_json_when_expired()
    {
        $tenant = Tenant::find(1);
        $tenant->update(['trial_ends_at' => now()->subDays(5)]);

        $user = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'pro',
            'trial_ends_at' => now()->subDays(5),
        ]);

        $request = Request::create('/client', 'GET');
        $request->setUserResolver(fn() => $user);
        $request->headers->set('Accept', 'application/json');

        $middleware = new CheckTrialExpired();
        $response = $middleware->handle($request, fn() => new Response('OK'));

        $this->assertEquals(402, $response->getStatusCode());
    }

    /** @test */
    public function trial_middleware_allows_billing_route_when_expired()
    {
        $tenant = Tenant::find(1);
        $tenant->update(['trial_ends_at' => now()->subDays(5)]);

        $user = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'pro',
            'trial_ends_at' => now()->subDays(5),
        ]);

        $this->actingAs($user);

        // Le billing devrait être accessible même avec trial expiré
        $response = $this->get('/client/billing');
        // Ne devrait pas redirect vers billing (on y est déjà)
        $this->assertNotEquals(302, $response->getStatusCode());
    }
}
