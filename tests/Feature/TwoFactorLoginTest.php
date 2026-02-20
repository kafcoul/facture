<?php

namespace Tests\Feature;

use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests for Batch 7 Fixes:
 * - Fix 22: Login redirects to 2FA challenge when user has 2FA enabled
 * - Fix 23: TwoFactorController uses correct route names (client.settings.index)
 * - Fix 24: LoginController validates credentials without bypassing 2FA
 */
class TwoFactorLoginTest extends TestCase
{
    use RefreshDatabase;

    protected User $userWithout2FA;
    protected User $userWith2FA;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'id'        => 1,
            'name'      => 'Test Tenant',
            'slug'      => 'test-tenant',
            'is_active' => true,
            'plan'      => 'pro',
        ]);

        // Utilisateur SANS 2FA
        $this->userWithout2FA = User::factory()->create([
            'tenant_id' => 1,
            'role'      => 'client',
            'plan'      => 'pro',
            'is_active' => true,
            'email'     => 'no2fa@test.com',
            'password'  => Hash::make('password123'),
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        // Utilisateur AVEC 2FA activé et confirmé
        $this->userWith2FA = User::factory()->create([
            'tenant_id' => 1,
            'role'      => 'client',
            'plan'      => 'pro',
            'is_active' => true,
            'email'     => 'with2fa@test.com',
            'password'  => Hash::make('password123'),
            'two_factor_secret'         => encrypt('JBSWY3DPEHPK3PXP'),
            'two_factor_confirmed_at'   => now(),
            'two_factor_recovery_codes' => encrypt(json_encode(['CODE1', 'CODE2'])),
        ]);
    }

    // ═══════════════════════════════════════════════════════
    //  FIX 22: Login + 2FA challenge redirect
    // ═══════════════════════════════════════════════════════

    /** @test */
    public function user_without_2fa_is_logged_in_directly()
    {
        $response = $this->post('/login', [
            'email'    => 'no2fa@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/client');
        $this->assertAuthenticatedAs($this->userWithout2FA);
    }

    /** @test */
    public function user_with_2fa_is_redirected_to_challenge_page()
    {
        $response = $this->post('/login', [
            'email'    => 'with2fa@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('two-factor.login'));
        // L'utilisateur ne doit PAS être authentifié à ce stade
        $this->assertGuest();
    }

    /** @test */
    public function user_with_2fa_has_login_id_stored_in_session()
    {
        $response = $this->post('/login', [
            'email'    => 'with2fa@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHas('login.id', $this->userWith2FA->id);
        $response->assertSessionHas('login.remember');
    }

    /** @test */
    public function login_with_remember_stores_remember_flag_in_session()
    {
        $response = $this->post('/login', [
            'email'    => 'with2fa@test.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        $response->assertSessionHas('login.remember', true);
    }

    /** @test */
    public function login_with_wrong_password_fails_for_2fa_user()
    {
        $response = $this->post('/login', [
            'email'    => 'with2fa@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function login_with_wrong_password_fails_for_regular_user()
    {
        $response = $this->post('/login', [
            'email'    => 'no2fa@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function login_with_nonexistent_email_fails()
    {
        $response = $this->post('/login', [
            'email'    => 'nobody@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function admin_user_without_2fa_is_redirected_to_admin()
    {
        $admin = User::factory()->create([
            'tenant_id' => 1,
            'role'      => 'admin',
            'is_active' => true,
            'email'     => 'admin@test.com',
            'password'  => Hash::make('password123'),
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    /** @test */
    public function user_with_secret_but_unconfirmed_2fa_logs_in_directly()
    {
        // 2FA secret exists but not yet confirmed — should NOT trigger challenge
        $user = User::factory()->create([
            'tenant_id' => 1,
            'role'      => 'client',
            'plan'      => 'pro',
            'is_active' => true,
            'email'     => 'pending2fa@test.com',
            'password'  => Hash::make('password123'),
            'two_factor_secret'       => encrypt('JBSWY3DPEHPK3PXP'),
            'two_factor_confirmed_at' => null, // NOT confirmed
        ]);

        $response = $this->post('/login', [
            'email'    => 'pending2fa@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/client');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function last_login_at_is_updated_for_regular_login()
    {
        $this->assertNull($this->userWithout2FA->last_login_at);

        $this->post('/login', [
            'email'    => 'no2fa@test.com',
            'password' => 'password123',
        ]);

        $this->userWithout2FA->refresh();
        $this->assertNotNull($this->userWithout2FA->last_login_at);
    }

    // ═══════════════════════════════════════════════════════
    //  FIX 22: 2FA challenge page accessible
    // ═══════════════════════════════════════════════════════

    /** @test */
    public function two_factor_challenge_page_is_accessible_with_session()
    {
        // Simuler l'état après validation des identifiants
        $response = $this->withSession([
            'login.id'       => $this->userWith2FA->id,
            'login.remember' => false,
        ])->get('/two-factor-challenge');

        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════
    //  FIX 23: TwoFactorController uses correct routes
    // ═══════════════════════════════════════════════════════

    /** @test */
    public function twofactor_enable_redirects_to_client_settings_when_already_enabled()
    {
        $response = $this->actingAs($this->userWith2FA)
            ->get('/client/two-factor/enable');

        $response->assertRedirect(route('client.settings.index'));
        $response->assertSessionHas('info');
    }

    /** @test */
    public function twofactor_disable_redirects_to_client_settings_on_success()
    {
        $response = $this->actingAs($this->userWith2FA)
            ->delete('/client/two-factor/disable', [
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('client.settings.index'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function twofactor_disable_fails_with_wrong_password()
    {
        $response = $this->actingAs($this->userWith2FA)
            ->delete('/client/two-factor/disable', [
                'password' => 'wrongpassword',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function twofactor_recovery_codes_redirects_when_2fa_not_enabled()
    {
        $response = $this->actingAs($this->userWithout2FA)
            ->get('/client/two-factor/recovery-codes');

        $response->assertRedirect(route('client.settings.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function twofactor_confirm_redirects_when_session_expired()
    {
        // Pas de secret en session => "Session expirée"
        $response = $this->actingAs($this->userWithout2FA)
            ->post('/client/two-factor/confirm', [
                'code' => '123456',
            ]);

        $response->assertRedirect(route('client.settings.index'));
        $response->assertSessionHas('error');
    }

    // ═══════════════════════════════════════════════════════
    //  FIX 24: Login form works correctly
    // ═══════════════════════════════════════════════════════

    /** @test */
    public function login_page_displays_correctly()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('email');
        $response->assertSee('password');
    }

    /** @test */
    public function login_requires_email_and_password()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
    }
}
