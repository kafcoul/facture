<?php

namespace Tests\Feature;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\CreditNote;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Models\RecurringInvoice;
use App\Models\ApiKey;
use App\Models\User;
use App\Models\WebhookLog;
use App\Services\PlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $client_user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'admin',
            'plan' => 'enterprise',
            'is_active' => true,
        ]);

        $this->client_user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'pro',
            'is_active' => true,
        ]);

        $this->client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'name' => 'Test Client SA',
            'email' => 'client@test.com',
        ]);
    }

    // ─── RecurringInvoice Model Tests ────

    /** @test */
    public function it_can_create_a_recurring_invoice()
    {
        $recurring = RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => now(),
            'next_due_date' => now()->addMonth(),
            'subtotal' => 10000,
            'tax' => 1800,
            'total' => 11800,
            'currency' => 'XOF',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('recurring_invoices', [
            'id' => $recurring->id,
            'frequency' => 'monthly',
            'currency' => 'XOF',
        ]);
    }

    /** @test */
    public function recurring_invoice_belongs_to_client()
    {
        $recurring = RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => now(),
            'next_due_date' => now()->addMonth(),
            'total' => 11800,
        ]);

        $this->assertEquals($this->client->id, $recurring->client->id);
        $this->assertEquals('Test Client SA', $recurring->client->name);
    }

    /** @test */
    public function recurring_invoice_scope_due_returns_due_items()
    {
        // Due (should be returned)
        RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => now()->subMonth(),
            'next_due_date' => now()->subDay(),
            'total' => 10000,
            'is_active' => true,
        ]);

        // Not due (should NOT be returned)
        RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => now(),
            'next_due_date' => now()->addMonth(),
            'total' => 10000,
            'is_active' => true,
        ]);

        // Inactive (should NOT be returned)
        RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => now()->subMonth(),
            'next_due_date' => now()->subDay(),
            'total' => 10000,
            'is_active' => false,
        ]);

        $due = RecurringInvoice::due()->get();
        $this->assertCount(1, $due);
    }

    /** @test */
    public function recurring_invoice_can_generate_checks_active()
    {
        $recurring = RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => now(),
            'next_due_date' => now()->addMonth(),
            'total' => 10000,
            'is_active' => true,
        ]);

        $this->assertTrue($recurring->canGenerate());

        $recurring->update(['is_active' => false]);
        $this->assertFalse($recurring->canGenerate());
    }

    /** @test */
    public function recurring_invoice_respects_occurrences_limit()
    {
        $recurring = RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => now(),
            'next_due_date' => now()->addMonth(),
            'total' => 10000,
            'is_active' => true,
            'occurrences_limit' => 3,
            'occurrences_count' => 2,
        ]);

        $this->assertTrue($recurring->canGenerate());

        $recurring->update(['occurrences_count' => 3]);
        $this->assertFalse($recurring->canGenerate());
    }

    /** @test */
    public function recurring_invoice_frequency_label_attribute()
    {
        $recurring = RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'quarterly',
            'start_date' => now(),
            'next_due_date' => now()->addMonths(3),
            'total' => 10000,
            'is_active' => true,
        ]);

        $this->assertEquals('Trimestriel', $recurring->frequency_label);
    }

    // ─── CreditNote Model Tests ────

    /** @test */
    public function it_can_create_a_credit_note()
    {
        $creditNote = CreditNote::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'AV-00001',
            'reason' => 'error',
            'status' => 'draft',
            'subtotal' => 5000,
            'tax' => 900,
            'total' => 5900,
            'currency' => 'XOF',
        ]);

        $this->assertDatabaseHas('credit_notes', [
            'id' => $creditNote->id,
            'number' => 'AV-00001',
            'reason' => 'error',
            'status' => 'draft',
        ]);
        $this->assertNotNull($creditNote->uuid);
    }

    /** @test */
    public function credit_note_belongs_to_client()
    {
        $creditNote = CreditNote::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'AV-00002',
            'reason' => 'return',
            'total' => 3000,
        ]);

        $this->assertEquals($this->client->id, $creditNote->client->id);
    }

    /** @test */
    public function credit_note_can_be_linked_to_invoice()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-CN-001',
            'subtotal' => 10000,
            'tax' => 1800,
            'total' => 11800,
            'status' => 'paid',
            'due_date' => now()->addDays(30),
        ]);

        $creditNote = CreditNote::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'invoice_id' => $invoice->id,
            'number' => 'AV-00003',
            'reason' => 'error',
            'total' => 5000,
        ]);

        $this->assertEquals($invoice->id, $creditNote->invoice->id);
    }

    /** @test */
    public function credit_note_scope_issued()
    {
        CreditNote::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'AV-DRAFT',
            'reason' => 'error',
            'status' => 'draft',
            'total' => 1000,
        ]);

        CreditNote::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'AV-ISSUED',
            'reason' => 'return',
            'status' => 'issued',
            'total' => 2000,
        ]);

        $this->assertCount(1, CreditNote::issued()->get());
        $this->assertCount(1, CreditNote::draft()->get());
    }

    /** @test */
    public function credit_note_statuses_constant_is_complete()
    {
        $expected = ['draft', 'issued', 'applied', 'cancelled'];
        $this->assertEquals($expected, array_keys(CreditNote::STATUSES));
    }

    /** @test */
    public function credit_note_reasons_constant_is_complete()
    {
        $expected = ['error', 'return', 'discount', 'cancellation', 'duplicate', 'other'];
        $this->assertEquals($expected, array_keys(CreditNote::REASONS));
    }

    // ─── Quote (Invoice type=quote) Tests ────

    /** @test */
    public function it_can_create_a_quote()
    {
        $quote = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'DEV-00001',
            'type' => 'quote',
            'status' => 'draft',
            'subtotal' => 15000,
            'tax' => 2700,
            'total' => 17700,
            'currency' => 'XOF',
            'due_date' => now()->addDays(30),
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $quote->id,
            'type' => 'quote',
            'number' => 'DEV-00001',
        ]);
    }

    /** @test */
    public function quotes_are_separate_from_invoices_by_type()
    {
        Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-TYPE-001',
            'type' => 'invoice',
            'total' => 10000,
            'status' => 'draft',
            'due_date' => now()->addDays(30),
        ]);

        Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'DEV-TYPE-001',
            'type' => 'quote',
            'total' => 5000,
            'status' => 'draft',
            'due_date' => now()->addDays(30),
        ]);

        $invoices = Invoice::where('type', 'invoice')->count();
        $quotes = Invoice::where('type', 'quote')->count();

        $this->assertEquals(1, $invoices);
        $this->assertEquals(1, $quotes);
    }

    // ─── ApiKey Model Tests ────

    /** @test */
    public function it_can_generate_api_key()
    {
        $result = ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Test API Key',
            'permissions' => ['invoices.*', 'clients.view'],
            'rate_limit_per_minute' => 60,
        ]);

        $this->assertArrayHasKey('api_key', $result);
        $this->assertArrayHasKey('key', $result);
        $this->assertArrayHasKey('secret', $result);
        $this->assertStringStartsWith('inv_', $result['key']);
        $this->assertDatabaseHas('api_keys', [
            'name' => 'Test API Key',
        ]);
    }

    /** @test */
    public function api_key_can_verify_secret()
    {
        $result = ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Verify Test',
            'permissions' => ['*'],
        ]);

        $apiKey = $result['api_key'];
        $this->assertTrue($apiKey->verifySecret($result['secret']));
        $this->assertFalse($apiKey->verifySecret('wrong_secret'));
    }

    /** @test */
    public function api_key_validity_checks()
    {
        $result = ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Validity Test',
            'permissions' => ['*'],
            'is_active' => true,
        ]);

        $apiKey = $result['api_key'];
        $this->assertTrue($apiKey->isValid());

        // Revoke
        $apiKey->revoke();
        $this->assertFalse($apiKey->isValid());
    }

    /** @test */
    public function api_key_expired_is_not_valid()
    {
        $result = ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Expired Test',
            'permissions' => ['*'],
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);

        $apiKey = $result['api_key'];
        $this->assertFalse($apiKey->isValid());
    }

    /** @test */
    public function api_key_has_permission_check()
    {
        $result = ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Perm Test',
            'permissions' => ['invoices.*', 'clients.view'],
        ]);

        $apiKey = $result['api_key'];

        $this->assertTrue($apiKey->hasPermission('invoices.view'));
        $this->assertTrue($apiKey->hasPermission('invoices.create'));
        $this->assertTrue($apiKey->hasPermission('clients.view'));
        $this->assertFalse($apiKey->hasPermission('clients.create'));
        $this->assertFalse($apiKey->hasPermission('products.view'));
    }

    /** @test */
    public function api_key_wildcard_permission()
    {
        $result = ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Wildcard Test',
            'permissions' => ['*'],
        ]);

        $apiKey = $result['api_key'];
        $this->assertTrue($apiKey->hasPermission('invoices.view'));
        $this->assertTrue($apiKey->hasPermission('anything.goes'));
    }

    /** @test */
    public function api_key_records_usage()
    {
        $result = ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Usage Test',
            'permissions' => ['*'],
        ]);

        $apiKey = $result['api_key'];
        $this->assertEquals(0, $apiKey->usage_count);

        $apiKey->recordUsage();
        $apiKey->refresh();

        $this->assertEquals(1, $apiKey->usage_count);
        $this->assertNotNull($apiKey->last_used_at);
    }

    /** @test */
    public function api_key_scope_active()
    {
        ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Active Key',
            'permissions' => ['*'],
            'is_active' => true,
        ]);

        ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Inactive Key',
            'permissions' => ['*'],
            'is_active' => false,
        ]);

        $this->assertCount(1, ApiKey::active()->get());
    }

    /** @test */
    public function api_key_masked_key_attribute()
    {
        $result = ApiKey::generate([
            'tenant_id' => 1,
            'user_id' => $this->admin->id,
            'name' => 'Mask Test',
            'permissions' => ['*'],
        ]);

        $apiKey = $result['api_key'];
        $masked = $apiKey->masked_key;

        $this->assertStringStartsWith('inv_', $masked);
        $this->assertStringContainsString('...', $masked);
    }

    // ─── WebhookLog Model Tests ────

    /** @test */
    public function webhook_log_can_be_created()
    {
        $log = WebhookLog::log('stripe', 'payment_intent.succeeded', [
            'id' => 'pi_test',
            'amount' => 10000,
        ]);

        $this->assertDatabaseHas('webhook_logs', [
            'gateway' => 'stripe',
            'event_type' => 'payment_intent.succeeded',
            'processed' => true,
        ]);
    }

    // ─── Subscription (PlanService) Tests ────

    /** @test */
    public function plan_service_defines_three_plans()
    {
        $this->assertArrayHasKey('starter', PlanService::PLANS);
        $this->assertArrayHasKey('pro', PlanService::PLANS);
        $this->assertArrayHasKey('enterprise', PlanService::PLANS);
        $this->assertCount(3, PlanService::PLANS);
    }

    /** @test */
    public function starter_plan_is_free()
    {
        $this->assertEquals(0, PlanService::PLANS['starter']['price']);
    }

    /** @test */
    public function enterprise_has_all_features()
    {
        $features = PlanService::PLANS['enterprise']['features'];
        foreach ($features as $feature => $enabled) {
            $this->assertTrue($enabled, "Enterprise should have feature: $feature");
        }
    }

    /** @test */
    public function users_can_have_plans()
    {
        $starterUser = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'starter',
        ]);
        $proUser = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'pro',
        ]);

        $this->assertEquals('starter', $starterUser->plan);
        $this->assertEquals('pro', $proUser->plan);
    }

    /** @test */
    public function user_trial_expiration()
    {
        $user = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'pro',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $this->assertTrue($user->trial_ends_at->isFuture());

        $expiredUser = User::factory()->create([
            'tenant_id' => 1,
            'plan' => 'pro',
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->assertTrue($expiredUser->trial_ends_at->isPast());
    }

    // ─── RecurringInvoice Frequencies ────

    /** @test */
    public function recurring_invoice_frequencies_constant()
    {
        $frequencies = RecurringInvoice::FREQUENCIES;
        $this->assertArrayHasKey('weekly', $frequencies);
        $this->assertArrayHasKey('monthly', $frequencies);
        $this->assertArrayHasKey('quarterly', $frequencies);
        $this->assertArrayHasKey('yearly', $frequencies);
    }

    /** @test */
    public function recurring_invoice_calculate_next_due_date()
    {
        $recurring = RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => '2026-01-01',
            'next_due_date' => '2026-01-01',
            'total' => 10000,
            'is_active' => true,
        ]);

        $next = $recurring->calculateNextDueDate();
        $this->assertEquals('2026-02-01', $next->format('Y-m-d'));
    }

    // ─── RecurringInvoice with items (JSON) ────

    /** @test */
    public function recurring_invoice_stores_items_as_json()
    {
        $items = [
            ['description' => 'Service mensuel', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000],
            ['description' => 'Support', 'quantity' => 1, 'unit_price' => 5000, 'total' => 5000],
        ];

        $recurring = RecurringInvoice::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'frequency' => 'monthly',
            'start_date' => now(),
            'next_due_date' => now()->addMonth(),
            'total' => 15000,
            'is_active' => true,
            'items' => $items,
        ]);

        $this->assertIsArray($recurring->items);
        $this->assertCount(2, $recurring->items);
        $this->assertEquals('Service mensuel', $recurring->items[0]['description']);
    }

    // ─── CreditNote with items (JSON) ────

    /** @test */
    public function credit_note_stores_items_as_json()
    {
        $items = [
            ['description' => 'Remboursement produit', 'quantity' => 1, 'unit_price' => 5000, 'total' => 5000],
        ];

        $creditNote = CreditNote::create([
            'tenant_id' => 1,
            'user_id' => $this->client_user->id,
            'client_id' => $this->client->id,
            'number' => 'AV-ITEMS',
            'reason' => 'return',
            'total' => 5000,
            'items' => $items,
        ]);

        $this->assertIsArray($creditNote->items);
        $this->assertCount(1, $creditNote->items);
    }
}
