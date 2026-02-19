<?php

namespace Tests\Feature;

use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════
    //  Registration (RegisterWithPlanController)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function registration_page_is_accessible()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_register_with_plan()
    {
        Notification::fake();
        Event::fake([Registered::class]);

        $response = $this->post(route('register.with-plan'), [
            'company_name' => 'Entreprise Dakar',
            'name' => 'Moussa Diop',
            'email' => 'moussa@dakar.sn',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'plan' => 'pro',
            'terms' => true,
        ]);

        // Vérifie la redirection vers le dashboard client
        $response->assertRedirect(route('client.index'));

        // Vérifie que le tenant est créé
        $this->assertDatabaseHas('tenants', [
            'name' => 'Entreprise Dakar',
            'plan' => 'pro',
            'is_active' => true,
        ]);

        // Vérifie que l'utilisateur est créé
        $this->assertDatabaseHas('users', [
            'name' => 'Moussa Diop',
            'email' => 'moussa@dakar.sn',
            'role' => 'admin',
            'plan' => 'pro',
            'is_active' => true,
        ]);

        // Vérifie que l'événement Registered est déclenché
        Event::assertDispatched(Registered::class);

        // Vérifie que la notification de bienvenue est envoyée
        Notification::assertSentTo(
            User::where('email', 'moussa@dakar.sn')->first(),
            WelcomeNotification::class
        );

        // L'utilisateur est connecté automatiquement
        $this->assertAuthenticated();
    }

    /** @test */
    public function registration_creates_tenant_with_trial()
    {
        Notification::fake();

        $this->post(route('register.with-plan'), [
            'company_name' => 'SaaS Afrique',
            'name' => 'Awa Ndiaye',
            'email' => 'awa@saas.sn',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'plan' => 'starter',
            'terms' => true,
        ]);

        $tenant = Tenant::where('name', 'SaaS Afrique')->first();
        $this->assertNotNull($tenant);
        $this->assertNotNull($tenant->trial_ends_at);
        // Trial = 30 jours
        $this->assertTrue(
            $tenant->trial_ends_at->isBetween(now()->addDays(29), now()->addDays(31))
        );
    }

    /** @test */
    public function registration_validates_required_fields()
    {
        $response = $this->post(route('register.with-plan'), []);

        $response->assertSessionHasErrors([
            'company_name',
            'name',
            'email',
            'password',
            'plan',
            'terms',
        ]);
    }

    /** @test */
    public function registration_requires_valid_email()
    {
        $response = $this->post(route('register.with-plan'), [
            'company_name' => 'Test',
            'name' => 'Test',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'plan' => 'pro',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function registration_requires_unique_email()
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $response = $this->post(route('register.with-plan'), [
            'company_name' => 'Test',
            'name' => 'Test',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'plan' => 'pro',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function registration_requires_password_confirmation()
    {
        $response = $this->post(route('register.with-plan'), [
            'company_name' => 'Test',
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
            'plan' => 'pro',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_requires_minimum_password_length()
    {
        $response = $this->post(route('register.with-plan'), [
            'company_name' => 'Test',
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'plan' => 'pro',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_requires_valid_plan()
    {
        $response = $this->post(route('register.with-plan'), [
            'company_name' => 'Test',
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'plan' => 'invalid_plan',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('plan');
    }

    /** @test */
    public function registration_requires_terms_accepted()
    {
        $response = $this->post(route('register.with-plan'), [
            'company_name' => 'Test',
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'plan' => 'pro',
            // terms not provided
        ]);

        $response->assertSessionHasErrors('terms');
    }

    /** @test */
    public function registration_supports_all_plan_types()
    {
        Notification::fake();

        foreach (['starter', 'pro', 'enterprise'] as $plan) {
            $response = $this->post(route('register.with-plan'), [
                'company_name' => "Company $plan",
                'name' => "User $plan",
                'email' => "$plan@test.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'plan' => $plan,
                'terms' => true,
            ]);

            $response->assertRedirect(route('client.index'));
            $this->assertDatabaseHas('users', [
                'email' => "$plan@test.com",
                'plan' => $plan,
            ]);

            // Logout pour le prochain test
            auth()->logout();
        }
    }

    // ═══════════════════════════════════════════════════
    //  Login (LoginController)
    // ═══════════════════════════════════════════════════

    /** @test */
    public function login_page_is_accessible()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'moussa@dakar.sn',
            'password' => bcrypt('password123'),
            'role' => 'client',
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'moussa@dakar.sn',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/client');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function admin_user_is_redirected_to_admin_dashboard()
    {
        $user = User::factory()->create([
            'email' => 'admin@dakar.sn',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'admin@dakar.sn',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_fails_with_invalid_password()
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'test@test.com',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function login_fails_with_nonexistent_email()
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'nobody@nowhere.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function login_validates_required_fields()
    {
        $response = $this->post(route('login.submit'), []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function login_updates_last_login_at()
    {
        $user = User::factory()->create([
            'email' => 'moussa@dakar.sn',
            'password' => bcrypt('password123'),
            'last_login_at' => null,
        ]);

        $this->post(route('login.submit'), [
            'email' => 'moussa@dakar.sn',
            'password' => 'password123',
        ]);

        $user->refresh();
        $this->assertNotNull($user->last_login_at);
    }

    /** @test */
    public function login_supports_remember_me()
    {
        $user = User::factory()->create([
            'email' => 'moussa@dakar.sn',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'moussa@dakar.sn',
            'password' => 'password123',
            'remember' => true,
        ]);

        // La session doit être active et le cookie remember_token présent
        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    // ═══════════════════════════════════════════════════
    //  Logout
    // ═══════════════════════════════════════════════════

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function guest_cannot_access_logout()
    {
        $response = $this->post(route('logout'));

        // Middleware auth redirige vers login
        $response->assertRedirect(route('login'));
    }

    // ═══════════════════════════════════════════════════
    //  Route Protection / Middleware Guards
    // ═══════════════════════════════════════════════════

    /** @test */
    public function guest_cannot_access_client_dashboard()
    {
        $response = $this->get('/client');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_sees_dashboard_instead_of_landing()
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/client');
    }

    /** @test */
    public function admin_user_sees_admin_instead_of_landing()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/admin');
    }

    /** @test */
    public function guest_can_access_public_pages()
    {
        $this->get(route('home'))->assertStatus(200);
        $this->get(route('about'))->assertStatus(200);
        $this->get(route('legal.terms'))->assertStatus(200);
        $this->get(route('legal.privacy'))->assertStatus(200);
        $this->get(route('legal.mentions'))->assertStatus(200);
    }
}
