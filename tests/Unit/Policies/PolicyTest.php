<?php

namespace Tests\Unit\Policies;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
            'plan' => 'pro',
        ]);

        // Créer un second tenant pour l'isolation
        \App\Domain\Tenant\Models\Tenant::create([
            'id' => 2,
            'name' => 'Other Company',
            'slug' => 'other',
            'is_active' => true,
        ]);

        $this->otherUser = User::factory()->create([
            'tenant_id' => 2,
            'role' => 'client',
            'plan' => 'pro',
        ]);
    }

    // ═══════════════════════════════════════════════════
    //  ClientPolicy
    // ═══════════════════════════════════════════════════

    /** @test */
    public function client_policy_allows_view_any()
    {
        $policy = new ClientPolicy();
        $this->assertTrue($policy->viewAny($this->user));
    }

    /** @test */
    public function client_policy_allows_view_own_tenant_client()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Mon Client',
            'email' => 'client@test.com',
        ]);

        $policy = new ClientPolicy();
        $this->assertTrue($policy->view($this->user, $client));
    }

    /** @test */
    public function client_policy_denies_view_other_tenant_client()
    {
        $client = Client::create([
            'tenant_id' => 2,
            'user_id' => $this->otherUser->id,
            'name' => 'Autre Client',
            'email' => 'other@test.com',
        ]);

        $policy = new ClientPolicy();
        $this->assertFalse($policy->view($this->user, $client));
    }

    /** @test */
    public function client_policy_allows_create()
    {
        $policy = new ClientPolicy();
        $this->assertTrue($policy->create($this->user));
    }

    /** @test */
    public function client_policy_allows_update_own_tenant()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Mon Client',
            'email' => 'client@test.com',
        ]);

        $policy = new ClientPolicy();
        $this->assertTrue($policy->update($this->user, $client));
    }

    /** @test */
    public function client_policy_denies_update_other_tenant()
    {
        $client = Client::create([
            'tenant_id' => 2,
            'user_id' => $this->otherUser->id,
            'name' => 'Autre Client',
            'email' => 'other@test.com',
        ]);

        $policy = new ClientPolicy();
        $this->assertFalse($policy->update($this->user, $client));
    }

    /** @test */
    public function client_policy_allows_delete_own_tenant()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Mon Client',
            'email' => 'client@test.com',
        ]);

        $policy = new ClientPolicy();
        $this->assertTrue($policy->delete($this->user, $client));
    }

    /** @test */
    public function client_policy_force_delete_requires_admin()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Mon Client',
            'email' => 'client@test.com',
        ]);

        $policy = new ClientPolicy();

        // Non-admin ne peut pas forceDelete
        $this->assertFalse($policy->forceDelete($this->user, $client));

        // Admin peut forceDelete
        $admin = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'admin',
        ]);
        $this->assertTrue($policy->forceDelete($admin, $client));
    }

    // ═══════════════════════════════════════════════════
    //  InvoicePolicy
    // ═══════════════════════════════════════════════════

    /** @test */
    public function invoice_policy_allows_view_own_tenant_invoice()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-TEST-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 1000,
            'tax' => 100,
            'total' => 1100,
            'status' => 'draft',
        ]);

        $policy = new InvoicePolicy();
        $this->assertTrue($policy->view($this->user, $invoice));
    }

    /** @test */
    public function invoice_policy_denies_view_other_tenant()
    {
        $client = Client::create([
            'tenant_id' => 2,
            'user_id' => $this->otherUser->id,
            'name' => 'Client 2',
            'email' => 'c2@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 2,
            'user_id' => $this->otherUser->id,
            'client_id' => $client->id,
            'number' => 'INV-OTHER-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 1000,
            'tax' => 100,
            'total' => 1100,
            'status' => 'draft',
        ]);

        $policy = new InvoicePolicy();
        $this->assertFalse($policy->view($this->user, $invoice));
    }

    /** @test */
    public function invoice_policy_allows_update_draft_invoice()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-DRAFT-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 1000,
            'tax' => 100,
            'total' => 1100,
            'status' => 'draft',
        ]);

        $policy = new InvoicePolicy();
        $this->assertTrue($policy->update($this->user, $invoice));
    }

    /** @test */
    public function invoice_policy_allows_update_sent_invoice()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-SENT-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 1000,
            'tax' => 100,
            'total' => 1100,
            'status' => 'sent',
        ]);

        $policy = new InvoicePolicy();
        $this->assertTrue($policy->update($this->user, $invoice));
    }

    /** @test */
    public function invoice_policy_denies_update_paid_invoice()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-PAID-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 1000,
            'tax' => 100,
            'total' => 1100,
            'status' => 'paid',
        ]);

        $policy = new InvoicePolicy();
        $this->assertFalse($policy->update($this->user, $invoice));
    }

    /** @test */
    public function invoice_policy_denies_update_cancelled_invoice()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        // Simulate a cancelled invoice by creating draft then manually setting status
        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'number' => 'INV-CANCEL-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 1000,
            'tax' => 100,
            'total' => 1100,
            'status' => 'draft',
        ]);
        // Force status to cancelled for policy test (bypassing enum constraint)
        $invoice->status = 'cancelled';

        $policy = new InvoicePolicy();
        $this->assertFalse($policy->update($this->user, $invoice));
    }

    /** @test */
    public function invoice_policy_allows_delete_only_drafts()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $policy = new InvoicePolicy();

        // Draft: can delete
        $draft = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-D1', 'due_date' => now()->addDays(30),
            'subtotal' => 100, 'tax' => 10, 'total' => 110, 'status' => 'draft',
        ]);
        $this->assertTrue($policy->delete($this->user, $draft));

        // Sent: cannot delete
        $sent = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-S1', 'due_date' => now()->addDays(30),
            'subtotal' => 100, 'tax' => 10, 'total' => 110, 'status' => 'sent',
        ]);
        $this->assertFalse($policy->delete($this->user, $sent));

        // Paid: cannot delete
        $paid = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-P1', 'due_date' => now()->addDays(30),
            'subtotal' => 100, 'tax' => 10, 'total' => 110, 'status' => 'paid',
        ]);
        $this->assertFalse($policy->delete($this->user, $paid));
    }

    /** @test */
    public function invoice_policy_send_allowed_for_draft_and_sent()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $policy = new InvoicePolicy();

        $draft = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-SEND1', 'due_date' => now()->addDays(30),
            'subtotal' => 100, 'tax' => 10, 'total' => 110, 'status' => 'draft',
        ]);
        $this->assertTrue($policy->send($this->user, $draft));

        $sent = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-SEND2', 'due_date' => now()->addDays(30),
            'subtotal' => 100, 'tax' => 10, 'total' => 110, 'status' => 'sent',
        ]);
        $this->assertTrue($policy->send($this->user, $sent));
    }

    /** @test */
    public function invoice_policy_send_denied_for_paid()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $paid = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-SENDPAID', 'due_date' => now()->addDays(30),
            'subtotal' => 100, 'tax' => 10, 'total' => 110, 'status' => 'paid',
        ]);

        $policy = new InvoicePolicy();
        $this->assertFalse($policy->send($this->user, $paid));
    }

    /** @test */
    public function invoice_policy_duplicate_allowed_for_own_tenant()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-DUP1', 'due_date' => now()->addDays(30),
            'subtotal' => 100, 'tax' => 10, 'total' => 110, 'status' => 'paid',
        ]);

        $policy = new InvoicePolicy();
        $this->assertTrue($policy->duplicate($this->user, $invoice));
    }

    // ═══════════════════════════════════════════════════
    //  ProductPolicy
    // ═══════════════════════════════════════════════════

    /** @test */
    public function product_policy_allows_view_own_tenant_product()
    {
        $product = Product::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Produit Test',
            'price' => 5000,
            'unit_price' => 5000,
        ]);

        $policy = new ProductPolicy();
        $this->assertTrue($policy->view($this->user, $product));
    }

    /** @test */
    public function product_policy_denies_view_other_tenant_product()
    {
        $product = Product::create([
            'tenant_id' => 2,
            'user_id' => $this->otherUser->id,
            'name' => 'Autre Produit',
            'price' => 5000,
            'unit_price' => 5000,
        ]);

        $policy = new ProductPolicy();
        $this->assertFalse($policy->view($this->user, $product));
    }

    /** @test */
    public function product_policy_allows_update_own_tenant()
    {
        $product = Product::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Produit',
            'price' => 5000,
            'unit_price' => 5000,
        ]);

        $policy = new ProductPolicy();
        $this->assertTrue($policy->update($this->user, $product));
    }

    /** @test */
    public function product_policy_denies_update_other_tenant()
    {
        $product = Product::create([
            'tenant_id' => 2,
            'user_id' => $this->otherUser->id,
            'name' => 'Autre Produit',
            'price' => 5000,
            'unit_price' => 5000,
        ]);

        $policy = new ProductPolicy();
        $this->assertFalse($policy->update($this->user, $product));
    }

    // ═══════════════════════════════════════════════════
    //  PaymentPolicy
    // ═══════════════════════════════════════════════════

    /** @test */
    public function payment_policy_allows_view_own_tenant()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-PAY1', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'paid',
        ]);

        $payment = Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $invoice->id,
            'user_id' => $this->user->id,
            'amount' => 1100,
            'gateway' => 'stripe',
            'status' => 'success',
            'transaction_id' => 'tx_test_001',
        ]);

        $policy = new PaymentPolicy();
        $this->assertTrue($policy->view($this->user, $payment));
    }

    /** @test */
    public function payment_policy_denies_view_other_tenant()
    {
        $client = Client::create([
            'tenant_id' => 2,
            'user_id' => $this->otherUser->id,
            'name' => 'Client 2',
            'email' => 'c2@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 2, 'user_id' => $this->otherUser->id, 'client_id' => $client->id,
            'number' => 'INV-PAY2', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'paid',
        ]);

        $payment = Payment::create([
            'tenant_id' => 2,
            'invoice_id' => $invoice->id,
            'user_id' => $this->otherUser->id,
            'amount' => 1100,
            'gateway' => 'stripe',
            'status' => 'success',
            'transaction_id' => 'tx_test_002',
        ]);

        $policy = new PaymentPolicy();
        $this->assertFalse($policy->view($this->user, $payment));
    }

    /** @test */
    public function payment_policy_delete_requires_admin()
    {
        $client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client',
            'email' => 'c@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => 1, 'user_id' => $this->user->id, 'client_id' => $client->id,
            'number' => 'INV-PAYDEL', 'due_date' => now()->addDays(30),
            'subtotal' => 1000, 'tax' => 100, 'total' => 1100, 'status' => 'paid',
        ]);

        $payment = Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $invoice->id,
            'user_id' => $this->user->id,
            'amount' => 1100,
            'gateway' => 'stripe',
            'status' => 'success',
            'transaction_id' => 'tx_test_del',
        ]);

        $policy = new PaymentPolicy();

        // Client role cannot delete
        $this->assertFalse($policy->delete($this->user, $payment));

        // Admin can delete
        $admin = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'admin',
        ]);
        $this->assertTrue($policy->delete($admin, $payment));
    }
}
