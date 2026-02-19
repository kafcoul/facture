<?php

namespace Tests\Feature;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Models\InvoiceItem;
use App\Models\Product;
use App\Models\User;
use App\Jobs\SendInvoiceEmailJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'pro',
        ]);

        $this->client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client Test',
            'email' => 'client@example.com',
            'phone' => '770000000',
            'address' => '123 Rue Test',
            'city' => 'Dakar',
            'country' => 'Sénégal',
        ]);
    }

    // ─── Index ──────────────────────────────────────────

    /** @test */
    public function authenticated_user_can_view_invoices_list()
    {
        Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-001', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get('/client/invoices');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.invoices.index');
        $response->assertViewHas('invoices');
    }

    /** @test */
    public function invoices_list_only_shows_own_tenant()
    {
        // Facture du tenant 1
        Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-MY', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        // Facture d'un autre tenant
        \App\Domain\Tenant\Models\Tenant::create([
            'id' => 2, 'name' => 'Other', 'slug' => 'other', 'is_active' => true,
        ]);
        $otherUser = User::factory()->create(['tenant_id' => 2]);
        $otherClient = Client::create([
            'tenant_id' => 2, 'user_id' => $otherUser->id,
            'name' => 'Other Client', 'email' => 'other@test.com',
        ]);
        Invoice::create([
            'tenant_id' => 2, 'user_id' => $otherUser->id, 'client_id' => $otherClient->id,
            'number' => 'INV-OTHER', 'due_date' => now()->addDays(30),
            'subtotal' => 2000, 'tax' => 200, 'total' => 2200, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get('/client/invoices');

        $response->assertStatus(200);
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->total() === 1
                && $invoices->first()->number === 'INV-MY';
        });
    }

    /** @test */
    public function invoices_can_be_filtered_by_status()
    {
        Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-DRAFT', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);
        Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-PAID', 'due_date' => now()->addDays(30),
            'subtotal' => 2000, 'tax' => 200, 'total' => 2200, 'status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->get('/client/invoices?status=draft');

        $response->assertStatus(200);
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->total() === 1;
        });
    }

    // ─── Show ───────────────────────────────────────────

    /** @test */
    public function user_can_view_own_invoice()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-SHOW', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get("/client/invoices/{$invoice->id}");

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.invoices.show');
    }

    /** @test */
    public function user_cannot_view_other_tenant_invoice()
    {
        \App\Domain\Tenant\Models\Tenant::create([
            'id' => 2, 'name' => 'Other', 'slug' => 'other', 'is_active' => true,
        ]);
        $otherUser = User::factory()->create(['tenant_id' => 2]);
        $otherClient = Client::create([
            'tenant_id' => 2, 'user_id' => $otherUser->id,
            'name' => 'Other Client', 'email' => 'other@test.com',
        ]);
        $otherInvoice = Invoice::create([
            'tenant_id' => 2, 'user_id' => $otherUser->id, 'client_id' => $otherClient->id,
            'number' => 'INV-DENIED', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get("/client/invoices/{$otherInvoice->id}");

        // 404 car le global scope BelongsToTenant filtre les factures des autres tenants
        $response->assertStatus(404);
    }

    // ─── Create ─────────────────────────────────────────

    /** @test */
    public function user_can_access_create_invoice_page()
    {
        $response = $this->actingAs($this->user)->get('/client/invoices/create');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.invoices.create');
        $response->assertViewHas('clients');
        $response->assertViewHas('products');
    }

    // ─── Destroy ────────────────────────────────────────

    /** @test */
    public function user_can_delete_draft_invoice()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-DELETE', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete("/client/invoices/{$invoice->id}");

        $response->assertRedirect(route('client.invoices.index'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function user_cannot_delete_sent_invoice()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-NODELETE', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'sent',
        ]);

        $response = $this->actingAs($this->user)->delete("/client/invoices/{$invoice->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_delete_paid_invoice()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-NODEL-PAID', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->delete("/client/invoices/{$invoice->id}");

        $response->assertStatus(403);
    }

    // ─── Send ───────────────────────────────────────────

    /** @test */
    public function user_can_send_draft_invoice()
    {
        Bus::fake([SendInvoiceEmailJob::class]);

        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-SEND', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->post("/client/invoices/{$invoice->id}/send");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $invoice->refresh();
        $this->assertEquals('sent', $invoice->status);

        Bus::assertDispatched(SendInvoiceEmailJob::class);
    }

    /** @test */
    public function send_fails_if_client_has_no_email()
    {
        $clientNoEmail = Client::create([
            'tenant_id' => 1, 'user_id' => $this->user->id,
            'name' => 'Client Sans Email', 'email' => null,
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $clientNoEmail->id,
            'number' => 'INV-NOEMAIL', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->post("/client/invoices/{$invoice->id}/send");

        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_cannot_send_paid_invoice()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-SEND-PAID', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->post("/client/invoices/{$invoice->id}/send");

        $response->assertStatus(403);
    }

    // ─── Duplicate ──────────────────────────────────────

    /** @test */
    public function user_can_duplicate_invoice()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-ORIG', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Service Test',
            'quantity' => 2,
            'unit_price' => 500,
            'tax_rate' => 10,
            'total' => 1000,
        ]);

        $response = $this->actingAs($this->user)->post("/client/invoices/{$invoice->id}/duplicate");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Nouvelle facture créée
        $this->assertDatabaseCount('invoices', 2);

        $newInvoice = Invoice::where('id', '!=', $invoice->id)->first();
        $this->assertEquals('draft', $newInvoice->status);
        $this->assertNotEquals($invoice->number, $newInvoice->number);
    }

    // ─── Change Status ──────────────────────────────────

    /** @test */
    public function user_can_change_draft_to_sent()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-STATUS', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->patch("/client/invoices/{$invoice->id}/status", [
            'status' => 'sent',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $invoice->refresh();
        $this->assertEquals('sent', $invoice->status);
    }

    /** @test */
    public function user_can_change_sent_to_paid()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-TOPAID', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'sent',
        ]);

        $response = $this->actingAs($this->user)->patch("/client/invoices/{$invoice->id}/status", [
            'status' => 'paid',
        ]);

        $response->assertRedirect();
        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
    }

    /** @test */
    public function invalid_status_transition_is_rejected()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-INVALID', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'paid',
        ]);

        // paid → sent is not allowed
        $response = $this->actingAs($this->user)->patch("/client/invoices/{$invoice->id}/status", [
            'status' => 'sent',
        ]);

        $response->assertSessionHas('error');
        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
    }

    /** @test */
    public function draft_to_cancelled_is_allowed()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $this->client->id,
            'number' => 'INV-CANCEL', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->patch("/client/invoices/{$invoice->id}/status", [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect();
        $invoice->refresh();
        $this->assertEquals('cancelled', $invoice->status);
    }

    // ─── Search API ─────────────────────────────────────

    /** @test */
    public function search_clients_returns_matching_clients()
    {
        $response = $this->actingAs($this->user)->getJson('/client/api/clients/search?q=Test');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Client Test']);
    }

    /** @test */
    public function search_clients_returns_empty_for_no_match()
    {
        $response = $this->actingAs($this->user)->getJson('/client/api/clients/search?q=XYZNotExisting');

        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }

    /** @test */
    public function search_products_returns_matching_products()
    {
        Product::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Consultation',
            'description' => 'Service de consultation',
            'unit_price' => 25000,
            'price' => 25000,
            'tax_rate' => 18,
        ]);

        $response = $this->actingAs($this->user)->getJson('/client/api/products/search?q=Consul');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Consultation']);
    }

    // ─── Auth guards ────────────────────────────────────

    /** @test */
    public function invoices_require_authentication()
    {
        $response = $this->get('/client/invoices');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function invoice_create_requires_authentication()
    {
        $response = $this->get('/client/invoices/create');
        $response->assertRedirect('/login');
    }
}
