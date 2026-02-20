<?php

namespace Tests\Unit\Models;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Create tenant and user
        $this->user = User::factory()->create(['tenant_id' => 1]);
        
        // Create client
        $this->client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'phone' => '1234567890',
            'address' => '123 Test St',
            'city' => 'Test City',
            'country' => 'Test Country',
        ]);
    }

    /** @test */
    public function it_can_create_an_invoice()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 1000,
            'tax' => 100,
            'total' => 1100,
            'status' => 'draft',
        ]);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('INV-001', $invoice->number);
        $this->assertEquals(1100, $invoice->total);
        $this->assertEquals('draft', $invoice->status);
    }

    /** @test */
    public function it_belongs_to_a_client()
    {
        $invoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
        ]);

        $this->assertInstanceOf(Client::class, $invoice->client);
        $this->assertEquals($this->client->id, $invoice->client->id);
    }

    /** @test */
    public function it_has_many_items()
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
        ]);

        $item = new InvoiceItem();
        $item->invoice_id = $invoice->id;
        $item->description = 'Test Item';
        $item->quantity = 2;
        $item->unit_price = 500;
        $item->total = 1000;
        $item->save();

        // Recharger la relation items
        $invoice->load('items');

        $this->assertCount(1, $invoice->items);
        $this->assertEquals('Test Item', $invoice->items->first()->description);
    }

    /** @test */
    public function it_calculates_subtotal_correctly()
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
            'subtotal' => 0,
        ]);

        $item1 = new InvoiceItem();
        $item1->invoice_id = $invoice->id;
        $item1->description = 'Item 1';
        $item1->quantity = 2;
        $item1->unit_price = 100;
        $item1->total = 200;
        $item1->save();

        $item2 = new InvoiceItem();
        $item2->invoice_id = $invoice->id;
        $item2->description = 'Item 2';
        $item2->quantity = 3;
        $item2->unit_price = 150;
        $item2->total = 450;
        $item2->save();

        $calculatedSubtotal = $invoice->items->sum('total');
        
        $this->assertEquals(650, $calculatedSubtotal);
    }

    /** @test */
    public function it_can_be_marked_as_paid()
    {
        $invoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
            'status' => 'sent',
        ]);

        $invoice->update(['status' => 'paid']);

        $this->assertEquals('paid', $invoice->fresh()->status);
    }

    /** @test */
    public function it_can_detect_overdue_invoices()
    {
        $overdueInvoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
            'due_date' => now()->subDays(5),
            'status' => 'sent',
        ]);

        $currentInvoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
            'due_date' => now()->addDays(5),
            'status' => 'sent',
        ]);

        $this->assertTrue($overdueInvoice->due_date->isPast());
        $this->assertFalse($currentInvoice->due_date->isPast());
    }

    /** @test */
    public function it_has_tenant_isolation()
    {
        // Créer un deuxième tenant et client
        $tenant2 = \App\Domain\Tenant\Models\Tenant::create([
            'name' => 'Tenant 2',
            'slug' => 'tenant-2',
        ]);
        /** @var Client $client2 */
        $client2 = Client::create([
            'tenant_id' => $tenant2->id,
            'user_id' => \App\Models\User::factory()->create()->id,
            'name' => 'Client 2',
        ]);

        $invoice1 = Invoice::factory()->create(['tenant_id' => 1, 'client_id' => $this->client->id]);
        $invoice2 = Invoice::factory()->create(['tenant_id' => $tenant2->id, 'client_id' => $client2->id]);

        $tenant1Invoices = Invoice::where('tenant_id', 1)->get();
        
        $this->assertCount(1, $tenant1Invoices);
        $this->assertEquals($invoice1->id, $tenant1Invoices->first()->id);
    }

    /** @test */
    public function it_validates_status_transitions()
    {
        $validStatuses = ['draft', 'sent', 'viewed', 'partially_paid', 'paid', 'overdue'];
        
        $invoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
            'status' => 'draft',
        ]);

        foreach ($validStatuses as $status) {
            $invoice->update(['status' => $status]);
            $this->assertEquals($status, $invoice->fresh()->status);
        }
    }

    // ─── Accessor (Fix 11) ─────────────────────────────

    /** @test */
    public function invoice_number_accessor_returns_number_field()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-2026-042',
            'due_date' => now()->addDays(30),
            'subtotal' => 50000,
            'tax' => 9000,
            'total' => 59000,
            'status' => 'draft',
        ]);

        $this->assertEquals('INV-2026-042', $invoice->invoice_number);
        $this->assertEquals($invoice->number, $invoice->invoice_number);
    }

    /** @test */
    public function invoice_number_is_appended_to_array()
    {
        $invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-APPEND-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 10000,
            'tax' => 0,
            'total' => 10000,
            'status' => 'draft',
        ]);

        $array = $invoice->toArray();
        $this->assertArrayHasKey('invoice_number', $array);
        $this->assertEquals('INV-APPEND-001', $array['invoice_number']);
    }
}
