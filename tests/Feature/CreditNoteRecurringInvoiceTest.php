<?php

namespace Tests\Feature;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\CreditNote;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Models\RecurringInvoice;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for CreditNote & RecurringInvoice CRUD
 * Both features are gated to Pro+ plans.
 */
class CreditNoteRecurringInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $starterUser;
    protected User $proUser;
    protected Client $client;
    protected Invoice $invoice;
    protected Tenant $proTenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Starter tenant (default id=1 from seeder)
        $starterTenant = Tenant::find(1);
        $starterTenant->update(['plan' => 'starter']);

        $this->starterUser = User::factory()->create([
            'tenant_id' => 1,
            'role'      => 'client',
            'plan'      => 'starter',
            'is_active' => true,
        ]);

        // Pro tenant
        $this->proTenant = Tenant::create([
            'id'        => 2,
            'name'      => 'Pro Tenant',
            'slug'      => 'pro-tenant',
            'is_active' => true,
            'plan'      => 'pro',
        ]);

        $this->proUser = User::factory()->create([
            'tenant_id' => 2,
            'role'      => 'client',
            'plan'      => 'pro',
            'is_active' => true,
        ]);

        // Client for pro tenant
        $this->client = Client::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'name'      => 'Test Client',
            'email'     => 'testclient@example.com',
        ]);

        // Invoice for credit notes
        $this->invoice = Invoice::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'INV-00001',
            'due_date'  => now()->addDays(30),
            'subtotal'  => 100000,
            'tax'       => 0,
            'total'     => 100000,
            'status'    => 'sent',
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    //  PLAN GATING — Starter cannot access Pro+ features
    // ═══════════════════════════════════════════════════════════

    /** @test */
    public function starter_cannot_access_credit_notes()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/credit-notes');
        $response->assertRedirect(route('client.billing'));
    }

    /** @test */
    public function starter_cannot_access_recurring_invoices()
    {
        $response = $this->actingAs($this->starterUser)->get('/client/recurring-invoices');
        $response->assertRedirect(route('client.billing'));
    }

    // ═══════════════════════════════════════════════════════════
    //  CREDIT NOTES — Index
    // ═══════════════════════════════════════════════════════════

    /** @test */
    public function pro_can_access_credit_notes_index()
    {
        $response = $this->actingAs($this->proUser)->get('/client/credit-notes');
        $response->assertOk();
        $response->assertViewIs('dashboard.credit-notes.index');
    }

    /** @test */
    public function credit_notes_index_displays_notes()
    {
        CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00001',
            'status'    => 'draft',
            'reason'    => 'error',
            'subtotal'  => 50000,
            'tax'       => 0,
            'total'     => 50000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 50000, 'total' => 50000]],
        ]);

        $response = $this->actingAs($this->proUser)->get('/client/credit-notes');
        $response->assertSee('AV-00001');
        $response->assertSee('Test Client');
    }

    // ═══════════════════════════════════════════════════════════
    //  CREDIT NOTES — Create & Store
    // ═══════════════════════════════════════════════════════════

    /** @test */
    public function pro_can_access_credit_note_create_form()
    {
        $response = $this->actingAs($this->proUser)->get('/client/credit-notes/create');
        $response->assertOk();
        $response->assertViewIs('dashboard.credit-notes.create');
    }

    /** @test */
    public function pro_can_store_a_credit_note()
    {
        $response = $this->actingAs($this->proUser)->post('/client/credit-notes', [
            'client_id'  => $this->client->id,
            'invoice_id' => $this->invoice->id,
            'reason'     => 'error',
            'items'      => [
                ['description' => 'Correction', 'quantity' => 2, 'unit_price' => 10000],
            ],
            'notes' => 'Test note',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('credit_notes', [
            'tenant_id' => 2,
            'client_id' => $this->client->id,
            'reason'    => 'error',
            'status'    => 'draft',
            'total'     => 20000,
        ]);
    }

    /** @test */
    public function credit_note_store_validates_required_fields()
    {
        $response = $this->actingAs($this->proUser)->post('/client/credit-notes', []);

        $response->assertSessionHasErrors(['client_id', 'reason', 'items']);
    }

    /** @test */
    public function credit_note_auto_generates_number()
    {
        $this->actingAs($this->proUser)->post('/client/credit-notes', [
            'client_id' => $this->client->id,
            'reason'    => 'discount',
            'items'     => [
                ['description' => 'Item', 'quantity' => 1, 'unit_price' => 5000],
            ],
        ]);

        $cn = CreditNote::first();
        $this->assertNotNull($cn);
        $this->assertStringStartsWith('AV-', $cn->number);
    }

    // ═══════════════════════════════════════════════════════════
    //  CREDIT NOTES — Show, Edit, Update
    // ═══════════════════════════════════════════════════════════

    /** @test */
    public function pro_can_view_own_credit_note()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00001',
            'status'    => 'draft',
            'reason'    => 'error',
            'subtotal'  => 50000,
            'tax'       => 0,
            'total'     => 50000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 50000, 'total' => 50000]],
        ]);

        $response = $this->actingAs($this->proUser)->get("/client/credit-notes/{$cn->id}");
        $response->assertOk();
        $response->assertViewIs('dashboard.credit-notes.show');
        $response->assertSee('AV-00001');
    }

    /** @test */
    public function pro_can_edit_draft_credit_note()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00002',
            'status'    => 'draft',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Old', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->get("/client/credit-notes/{$cn->id}/edit");
        $response->assertOk();
    }

    /** @test */
    public function pro_can_update_draft_credit_note()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00003',
            'status'    => 'draft',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Old', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->put("/client/credit-notes/{$cn->id}", [
            'client_id' => $this->client->id,
            'reason'    => 'discount',
            'items'     => [
                ['description' => 'Updated', 'quantity' => 3, 'unit_price' => 5000],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $cn->refresh();
        $this->assertEquals('discount', $cn->reason);
        $this->assertEquals(15000, $cn->total);
    }

    /** @test */
    public function cannot_edit_non_draft_credit_note()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00004',
            'status'    => 'issued',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->get("/client/credit-notes/{$cn->id}/edit");
        $response->assertForbidden();
    }

    // ═══════════════════════════════════════════════════════════
    //  CREDIT NOTES — Status Changes
    // ═══════════════════════════════════════════════════════════

    /** @test */
    public function can_change_draft_to_issued()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00005',
            'status'    => 'draft',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->patch("/client/credit-notes/{$cn->id}/status", [
            'status' => 'issued',
        ]);

        $response->assertRedirect();
        $cn->refresh();
        $this->assertEquals('issued', $cn->status);
    }

    /** @test */
    public function can_change_issued_to_applied()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00006',
            'status'    => 'issued',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->patch("/client/credit-notes/{$cn->id}/status", [
            'status' => 'applied',
        ]);

        $response->assertRedirect();
        $cn->refresh();
        $this->assertEquals('applied', $cn->status);
    }

    /** @test */
    public function can_cancel_credit_note()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00007',
            'status'    => 'issued',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->patch("/client/credit-notes/{$cn->id}/status", [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect();
        $cn->refresh();
        $this->assertEquals('cancelled', $cn->status);
    }

    // ═══════════════════════════════════════════════════════════
    //  CREDIT NOTES — Delete
    // ═══════════════════════════════════════════════════════════

    /** @test */
    public function can_delete_draft_credit_note()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00008',
            'status'    => 'draft',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->delete("/client/credit-notes/{$cn->id}");
        $response->assertRedirect(route('client.credit-notes.index'));
        $this->assertSoftDeleted('credit_notes', ['id' => $cn->id]);
    }

    /** @test */
    public function cannot_delete_non_draft_credit_note()
    {
        $cn = CreditNote::create([
            'tenant_id' => 2,
            'user_id'   => $this->proUser->id,
            'client_id' => $this->client->id,
            'number'    => 'AV-00009',
            'status'    => 'issued',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->delete("/client/credit-notes/{$cn->id}");
        $response->assertForbidden();
    }

    // ═══════════════════════════════════════════════════════════
    //  CREDIT NOTES — Tenant Isolation
    // ═══════════════════════════════════════════════════════════

    /** @test */
    public function cannot_view_credit_note_from_other_tenant()
    {
        $otherTenant = Tenant::create([
            'name' => 'Other', 'slug' => 'other-tenant', 'is_active' => true, 'plan' => 'pro',
        ]);
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id, 'role' => 'client', 'plan' => 'pro', 'is_active' => true]);
        $otherClient = Client::create(['tenant_id' => $otherTenant->id, 'user_id' => $otherUser->id, 'name' => 'Other Client', 'email' => 'other@test.com']);

        $cn = CreditNote::create([
            'tenant_id' => $otherTenant->id,
            'user_id'   => $otherUser->id,
            'client_id' => $otherClient->id,
            'number'    => 'AV-OTHER',
            'status'    => 'draft',
            'reason'    => 'error',
            'subtotal'  => 10000,
            'tax'       => 0,
            'total'     => 10000,
            'currency'  => 'XOF',
            'items'     => [['description' => 'Test', 'quantity' => 1, 'unit_price' => 10000, 'total' => 10000]],
        ]);

        $response = $this->actingAs($this->proUser)->get("/client/credit-notes/{$cn->id}");
        // BelongsToTenant scope returns 404 (model not found) or 403
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    // ═══════════════════════════════════════════════════════════════
    //  RECURRING INVOICES — Index
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function pro_can_access_recurring_invoices_index()
    {
        $response = $this->actingAs($this->proUser)->get('/client/recurring-invoices');
        $response->assertOk();
        $response->assertViewIs('dashboard.recurring-invoices.index');
    }

    /** @test */
    public function recurring_invoices_index_displays_entries()
    {
        RecurringInvoice::create([
            'tenant_id'     => 2,
            'user_id'       => $this->proUser->id,
            'client_id'     => $this->client->id,
            'frequency'     => 'monthly',
            'start_date'    => now(),
            'next_due_date' => now(),
            'is_active'     => true,
            'auto_send'     => false,
            'subtotal'      => 100000,
            'tax'           => 0,
            'total'         => 100000,
            'currency'      => 'XOF',
            'items'         => [['description' => 'Service mensuel', 'quantity' => 1, 'unit_price' => 100000, 'total' => 100000]],
        ]);

        $response = $this->actingAs($this->proUser)->get('/client/recurring-invoices');
        $response->assertSee('Test Client');
    }

    // ═══════════════════════════════════════════════════════════════
    //  RECURRING INVOICES — Create & Store
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function pro_can_access_recurring_invoice_create_form()
    {
        $response = $this->actingAs($this->proUser)->get('/client/recurring-invoices/create');
        $response->assertOk();
        $response->assertViewIs('dashboard.recurring-invoices.create');
    }

    /** @test */
    public function pro_can_store_a_recurring_invoice()
    {
        $response = $this->actingAs($this->proUser)->post('/client/recurring-invoices', [
            'client_id'        => $this->client->id,
            'frequency'        => 'monthly',
            'start_date'       => now()->format('Y-m-d'),
            'occurrences_limit' => 12,
            'auto_send'        => true,
            'items'            => [
                ['description' => 'Service mensuel', 'quantity' => 1, 'unit_price' => 50000],
            ],
            'notes' => 'Abonnement annuel',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('recurring_invoices', [
            'tenant_id'        => 2,
            'client_id'        => $this->client->id,
            'frequency'        => 'monthly',
            'is_active'        => true,
            'auto_send'        => true,
            'occurrences_limit' => 12,
            'total'            => 50000,
        ]);
    }

    /** @test */
    public function recurring_invoice_store_validates_required_fields()
    {
        $response = $this->actingAs($this->proUser)->post('/client/recurring-invoices', []);

        $response->assertSessionHasErrors(['client_id', 'frequency', 'start_date', 'items']);
    }

    /** @test */
    public function recurring_invoice_validates_frequency_values()
    {
        $response = $this->actingAs($this->proUser)->post('/client/recurring-invoices', [
            'client_id'  => $this->client->id,
            'frequency'  => 'invalid_frequency',
            'start_date' => now()->format('Y-m-d'),
            'items'      => [['description' => 'X', 'quantity' => 1, 'unit_price' => 1000]],
        ]);

        $response->assertSessionHasErrors(['frequency']);
    }

    // ═══════════════════════════════════════════════════════════════
    //  RECURRING INVOICES — Show, Edit, Update
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function pro_can_view_own_recurring_invoice()
    {
        $ri = RecurringInvoice::create([
            'tenant_id'     => 2,
            'user_id'       => $this->proUser->id,
            'client_id'     => $this->client->id,
            'frequency'     => 'quarterly',
            'start_date'    => now(),
            'next_due_date' => now(),
            'is_active'     => true,
            'auto_send'     => false,
            'subtotal'      => 75000,
            'tax'           => 0,
            'total'         => 75000,
            'currency'      => 'XOF',
            'items'         => [['description' => 'Service', 'quantity' => 1, 'unit_price' => 75000, 'total' => 75000]],
        ]);

        $response = $this->actingAs($this->proUser)->get("/client/recurring-invoices/{$ri->id}");
        $response->assertOk();
        $response->assertViewIs('dashboard.recurring-invoices.show');
    }

    /** @test */
    public function pro_can_edit_recurring_invoice()
    {
        $ri = RecurringInvoice::create([
            'tenant_id'     => 2,
            'user_id'       => $this->proUser->id,
            'client_id'     => $this->client->id,
            'frequency'     => 'monthly',
            'start_date'    => now(),
            'next_due_date' => now(),
            'is_active'     => true,
            'auto_send'     => false,
            'subtotal'      => 50000,
            'tax'           => 0,
            'total'         => 50000,
            'currency'      => 'XOF',
            'items'         => [['description' => 'Service', 'quantity' => 1, 'unit_price' => 50000, 'total' => 50000]],
        ]);

        $response = $this->actingAs($this->proUser)->get("/client/recurring-invoices/{$ri->id}/edit");
        $response->assertOk();
        $response->assertViewIs('dashboard.recurring-invoices.edit');
    }

    /** @test */
    public function pro_can_update_recurring_invoice()
    {
        $ri = RecurringInvoice::create([
            'tenant_id'     => 2,
            'user_id'       => $this->proUser->id,
            'client_id'     => $this->client->id,
            'frequency'     => 'monthly',
            'start_date'    => now(),
            'next_due_date' => now(),
            'is_active'     => true,
            'auto_send'     => false,
            'subtotal'      => 50000,
            'tax'           => 0,
            'total'         => 50000,
            'currency'      => 'XOF',
            'items'         => [['description' => 'Old', 'quantity' => 1, 'unit_price' => 50000, 'total' => 50000]],
        ]);

        $response = $this->actingAs($this->proUser)->put("/client/recurring-invoices/{$ri->id}", [
            'client_id'  => $this->client->id,
            'frequency'  => 'quarterly',
            'auto_send'  => true,
            'items'      => [
                ['description' => 'Updated service', 'quantity' => 2, 'unit_price' => 30000],
            ],
            'notes' => 'Updated note',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $ri->refresh();
        $this->assertEquals('quarterly', $ri->frequency);
        $this->assertTrue((bool) $ri->auto_send);
        $this->assertEquals(60000, $ri->total);
    }

    // ═══════════════════════════════════════════════════════════════
    //  RECURRING INVOICES — Toggle Active
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function pro_can_toggle_recurring_invoice_active()
    {
        $ri = RecurringInvoice::create([
            'tenant_id'     => 2,
            'user_id'       => $this->proUser->id,
            'client_id'     => $this->client->id,
            'frequency'     => 'monthly',
            'start_date'    => now(),
            'next_due_date' => now(),
            'is_active'     => true,
            'auto_send'     => false,
            'subtotal'      => 50000,
            'tax'           => 0,
            'total'         => 50000,
            'currency'      => 'XOF',
            'items'         => [['description' => 'Service', 'quantity' => 1, 'unit_price' => 50000, 'total' => 50000]],
        ]);

        // Deactivate
        $response = $this->actingAs($this->proUser)->post("/client/recurring-invoices/{$ri->id}/toggle");
        $response->assertRedirect();
        $ri->refresh();
        $this->assertFalse($ri->is_active);

        // Reactivate
        $response = $this->actingAs($this->proUser)->post("/client/recurring-invoices/{$ri->id}/toggle");
        $response->assertRedirect();
        $ri->refresh();
        $this->assertTrue($ri->is_active);
    }

    // ═══════════════════════════════════════════════════════════════
    //  RECURRING INVOICES — Delete
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function pro_can_delete_recurring_invoice()
    {
        $ri = RecurringInvoice::create([
            'tenant_id'     => 2,
            'user_id'       => $this->proUser->id,
            'client_id'     => $this->client->id,
            'frequency'     => 'monthly',
            'start_date'    => now(),
            'next_due_date' => now(),
            'is_active'     => false,
            'auto_send'     => false,
            'subtotal'      => 50000,
            'tax'           => 0,
            'total'         => 50000,
            'currency'      => 'XOF',
            'items'         => [['description' => 'Service', 'quantity' => 1, 'unit_price' => 50000, 'total' => 50000]],
        ]);

        $response = $this->actingAs($this->proUser)->delete("/client/recurring-invoices/{$ri->id}");
        $response->assertRedirect(route('client.recurring-invoices.index'));
        $this->assertSoftDeleted('recurring_invoices', ['id' => $ri->id]);
    }

    // ═══════════════════════════════════════════════════════════════
    //  RECURRING INVOICES — Tenant Isolation
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function cannot_view_recurring_invoice_from_other_tenant()
    {
        $otherTenant = Tenant::create([
            'name' => 'Other RI', 'slug' => 'other-ri', 'is_active' => true, 'plan' => 'pro',
        ]);
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id, 'role' => 'client', 'plan' => 'pro', 'is_active' => true]);
        $otherClient = Client::create(['tenant_id' => $otherTenant->id, 'user_id' => $otherUser->id, 'name' => 'Other RI Client', 'email' => 'otherri@test.com']);

        $ri = RecurringInvoice::create([
            'tenant_id'     => $otherTenant->id,
            'user_id'       => $otherUser->id,
            'client_id'     => $otherClient->id,
            'frequency'     => 'monthly',
            'start_date'    => now(),
            'next_due_date' => now(),
            'is_active'     => true,
            'auto_send'     => false,
            'subtotal'      => 50000,
            'tax'           => 0,
            'total'         => 50000,
            'currency'      => 'XOF',
            'items'         => [['description' => 'Service', 'quantity' => 1, 'unit_price' => 50000, 'total' => 50000]],
        ]);

        $response = $this->actingAs($this->proUser)->get("/client/recurring-invoices/{$ri->id}");
        // BelongsToTenant scope returns 404 (model not found) or 403
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    // ═══════════════════════════════════════════════════════════════
    //  SIDEBAR — Links visible for Pro users
    // ═══════════════════════════════════════════════════════════════

    /** @test */
    public function sidebar_shows_credit_notes_link_for_pro()
    {
        $response = $this->actingAs($this->proUser)->get('/client');
        $response->assertOk();
        $response->assertSee('Avoirs');
        $response->assertSee('Factures récurrentes');
    }

    /** @test */
    public function sidebar_hides_credit_notes_link_for_starter()
    {
        $response = $this->actingAs($this->starterUser)->get('/client');
        $response->assertOk();
        $response->assertDontSee('Avoirs');
        $response->assertDontSee('Factures récurrentes');
    }
}
