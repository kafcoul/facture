<?php

namespace Tests\Feature;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'pro',
        ]);
    }

    // ─── Clients Export ────────────────────────────────

    /** @test */
    public function authenticated_user_can_export_clients_csv()
    {
        Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client Test Export',
            'email' => 'export@test.com',
            'phone' => '770000001',
            'city' => 'Dakar',
            'country' => 'Sénégal',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.clients'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="clients_' . date('Y-m-d') . '.csv"');
    }

    /** @test */
    public function client_export_contains_expected_headers()
    {
        $response = $this->actingAs($this->user)
            ->get(route('client.exports.clients'));

        $response->assertOk();
        $content = $response->streamedContent();

        // BOM + Header row
        $this->assertStringContainsString('Nom', $content);
        $this->assertStringContainsString('Email', $content);
        $this->assertStringContainsString('Entreprise', $content);
    }

    /** @test */
    public function client_export_filters_by_active_status()
    {
        Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Active Client',
            'email' => 'active@test.com',
            'is_active' => true,
        ]);

        Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Inactive Client',
            'email' => 'inactive@test.com',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.clients', ['is_active' => '1']));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Active Client', $content);
        $this->assertStringNotContainsString('Inactive Client', $content);
    }

    /** @test */
    public function unauthenticated_user_cannot_export_clients()
    {
        $response = $this->get(route('client.exports.clients'));
        $response->assertRedirect(route('login'));
    }

    // ─── Invoices Export ───────────────────────────────

    /** @test */
    public function authenticated_user_can_export_invoices_csv()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client Facture',
            'email' => 'facture@test.com',
        ]);

        Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-TEST-001',
            'subtotal' => 10000,
            'tax' => 1800,
            'total' => 11800,
            'status' => 'sent',
            'due_date' => now()->addDays(30),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.invoices'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('INV-TEST-001', $content);
    }

    /** @test */
    public function invoice_export_filters_by_status()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-PAID-001',
            'subtotal' => 5000,
            'tax' => 900,
            'total' => 5900,
            'status' => 'paid',
            'due_date' => now()->addDays(30),
        ]);

        Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-DRAFT-001',
            'subtotal' => 3000,
            'tax' => 540,
            'total' => 3540,
            'status' => 'draft',
            'due_date' => now()->addDays(30),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.invoices', ['status' => 'paid']));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('INV-PAID-001', $content);
        $this->assertStringNotContainsString('INV-DRAFT-001', $content);
    }

    /** @test */
    public function invoice_export_filters_by_date_range()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client Date',
            'email' => 'date@test.com',
        ]);

        Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-OLD',
            'subtotal' => 1000,
            'tax' => 180,
            'total' => 1180,
            'status' => 'sent',
            'issued_at' => '2023-01-15',
            'due_date' => '2023-02-15',
        ]);

        Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-RECENT',
            'subtotal' => 2000,
            'tax' => 360,
            'total' => 2360,
            'status' => 'sent',
            'issued_at' => '2024-06-01',
            'due_date' => '2024-07-01',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.invoices', ['from' => '2024-01-01', 'to' => '2024-12-31']));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('INV-RECENT', $content);
        $this->assertStringNotContainsString('INV-OLD', $content);
    }

    // ─── Products Export ───────────────────────────────

    /** @test */
    public function authenticated_user_can_export_products_csv()
    {
        Product::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Produit Export Test',
            'sku' => 'SKU-EXPORT-001',
            'unit_price' => 5000,
            'price' => 5000,
            'tax_rate' => 18,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.products'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('Produit Export Test', $content);
        $this->assertStringContainsString('SKU-EXPORT-001', $content);
    }

    /** @test */
    public function product_export_filters_by_active_status()
    {
        Product::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Active Product',
            'sku' => 'SKU-ACT',
            'unit_price' => 1000,
            'price' => 1000,
            'is_active' => true,
        ]);

        Product::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Inactive Product',
            'sku' => 'SKU-INACT',
            'unit_price' => 2000,
            'price' => 2000,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.products', ['is_active' => '1']));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Active Product', $content);
        $this->assertStringNotContainsString('Inactive Product', $content);
    }

    // ─── Payments Export ───────────────────────────────

    /** @test */
    public function authenticated_user_can_export_payments_csv()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client Payment',
            'email' => 'pay@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-PAY-001',
            'subtotal' => 10000,
            'tax' => 1800,
            'total' => 11800,
            'status' => 'paid',
            'due_date' => now()->addDays(30),
        ]);

        Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $invoice->id,
            'user_id' => $this->user->id,
            'amount' => 11800,
            'gateway' => 'wave',
            'transaction_id' => 'TXN-WAVE-001',
            'status' => 'completed',
            'currency' => 'XOF',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.payments'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('TXN-WAVE-001', $content);
    }

    /** @test */
    public function payment_export_filters_by_status()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client Pay Filter',
            'email' => 'payfilter@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-PF-001',
            'subtotal' => 5000,
            'tax' => 900,
            'total' => 5900,
            'status' => 'sent',
            'due_date' => now()->addDays(30),
        ]);

        Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $invoice->id,
            'user_id' => $this->user->id,
            'amount' => 5900,
            'gateway' => 'stripe',
            'transaction_id' => 'TXN-COMPLETED',
            'status' => 'completed',
            'currency' => 'XOF',
            'completed_at' => now(),
        ]);

        Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $invoice->id,
            'user_id' => $this->user->id,
            'amount' => 5900,
            'gateway' => 'stripe',
            'transaction_id' => 'TXN-PENDING',
            'status' => 'pending',
            'currency' => 'XOF',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.payments', ['status' => 'completed']));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('TXN-COMPLETED', $content);
        $this->assertStringNotContainsString('TXN-PENDING', $content);
    }

    /** @test */
    public function payment_export_filters_by_gateway()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client GW Filter',
            'email' => 'gw@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-GW-001',
            'subtotal' => 5000,
            'tax' => 900,
            'total' => 5900,
            'status' => 'paid',
            'due_date' => now()->addDays(30),
        ]);

        Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $invoice->id,
            'user_id' => $this->user->id,
            'amount' => 5900,
            'gateway' => 'wave',
            'transaction_id' => 'TXN-WAVE-FILTER',
            'status' => 'completed',
            'currency' => 'XOF',
        ]);

        Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $invoice->id,
            'user_id' => $this->user->id,
            'amount' => 5900,
            'gateway' => 'stripe',
            'transaction_id' => 'TXN-STRIPE-FILTER',
            'status' => 'completed',
            'currency' => 'EUR',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('client.exports.payments', ['gateway' => 'wave']));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('TXN-WAVE-FILTER', $content);
        $this->assertStringNotContainsString('TXN-STRIPE-FILTER', $content);
    }
}
