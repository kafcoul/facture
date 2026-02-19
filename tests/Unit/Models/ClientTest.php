<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;
    private Tenant $tenant;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
        ]);

        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->client = Client::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Test Client',
            'email' => 'client@test.com',
        ]);
    }

    /** @test */
    public function it_can_create_a_client()
    {
        $this->assertDatabaseHas('clients', [
            'name' => 'Test Client',
            'email' => 'client@test.com',
        ]);
    }

    /** @test */
    public function it_belongs_to_a_tenant()
    {
        $this->assertInstanceOf(Tenant::class, $this->client->tenant);
        $this->assertEquals($this->tenant->id, $this->client->tenant->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->client->user);
        $this->assertEquals($this->user->id, $this->client->user->id);
    }

    /** @test */
    public function it_has_many_invoices()
    {
        Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
        ]);

        Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
        ]);

        $this->assertCount(2, $this->client->invoices);
    }

    /** @test */
    public function it_can_get_full_name_with_company()
    {
        $this->client->update(['company' => 'Test Company']);
        
        $this->assertEquals('Test Company (Test Client)', $this->client->full_name);
    }

    /** @test */
    public function it_can_get_full_name_without_company()
    {
        $this->assertEquals('Test Client', $this->client->full_name);
    }

    /** @test */
    public function it_can_scope_active_clients()
    {
        $activeClient = Client::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Active Client',
            'is_active' => true,
        ]);

        $inactiveClient = Client::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Inactive Client',
            'is_active' => false,
        ]);

        $activeClients = Client::active()->get();

        $this->assertTrue($activeClients->contains($activeClient));
        $this->assertFalse($activeClients->contains($inactiveClient));
    }

    /** @test */
    public function it_calculates_unpaid_invoices_total()
    {
        Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
            'status' => 'sent',
            'total' => 1000.00,
        ]);

        Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
            'status' => 'sent',
            'total' => 500.00,
        ]);

        Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
            'status' => 'paid',
            'total' => 300.00,
        ]);

        $unpaidTotal = $this->client->getUnpaidInvoicesTotal();

        $this->assertEquals(1500.00, $unpaidTotal);
    }

    /** @test */
    public function it_has_tenant_isolation()
    {
        $tenant2 = Tenant::create([
            'name' => 'Tenant 2',
            'slug' => 'tenant-2',
        ]);

        $user2 = User::factory()->create([
            'tenant_id' => $tenant2->id,
        ]);

        $client1 = Client::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Client Tenant 1',
        ]);

        $client2 = Client::create([
            'tenant_id' => $tenant2->id,
            'user_id' => $user2->id,
            'name' => 'Client Tenant 2',
        ]);

        $tenant1Clients = Client::where('tenant_id', $this->tenant->id)->get();

        $this->assertCount(2, $tenant1Clients); // $this->client + $client1
        $this->assertTrue($tenant1Clients->contains($client1));
        $this->assertFalse($tenant1Clients->contains($client2));
    }

    /** @test */
    public function it_soft_deletes_clients()
    {
        $clientId = $this->client->id;

        $this->client->delete();

        $this->assertSoftDeleted('clients', ['id' => $clientId]);

        // Vérifier qu'on peut encore récupérer avec withTrashed
        $deletedClient = Client::withTrashed()->find($clientId);
        $this->assertNotNull($deletedClient);
        $this->assertNotNull($deletedClient->deleted_at);
    }
}
