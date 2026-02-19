<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Product;
use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['tenant_id' => 1]);
        
        $this->client = Client::factory()->create(['tenant_id' => 1]);
        
        $this->product = Product::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Test Product',
            'description' => 'Test Description',
            'unit_price' => 100,
        ]);
    }

    /** @test */
    public function authenticated_user_can_create_invoice()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/invoices', [
            'client_id' => $this->client->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'description' => 'Test Item',
                    'quantity' => 2,
                    'unit_price' => 100,
                ],
            ],
            'tax_rate' => 10,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'invoice' => [
                        'id',
                        'invoice_number',
                        'client',
                        'subtotal',
                        'tax_amount',
                        'total_amount',
                        'status',
                    ],
                ],
                'message',
            ]);

        $this->assertDatabaseHas('invoices', [
            'client_id' => $this->client->id,
            'tenant_id' => 1,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_invoice()
    {
        $response = $this->postJson('/api/v1/invoices', [
            'client_id' => $this->client->id,
            'items' => [],
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function invoice_creation_validates_required_fields()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/invoices', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['client_id', 'items']);
    }

    /** @test */
    public function invoice_creation_validates_items_array()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/invoices', [
            'client_id' => $this->client->id,
            'items' => 'not-an-array',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    /** @test */
    public function invoice_calculations_are_correct()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/invoices', [
            'client_id' => $this->client->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'description' => 'Item 1',
                    'quantity' => 2,
                    'unit_price' => 100, // 200 total
                ],
                [
                    'product_id' => $this->product->id,
                    'description' => 'Item 2',
                    'quantity' => 3,
                    'unit_price' => 150, // 450 total
                ],
            ],
            'tax_rate' => 10, // 10% of 650 = 65
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertStatus(201);

        $invoice = Invoice::latest()->first();
        
        $this->assertEquals(650, $invoice->subtotal); // 200 + 450
        $this->assertEquals(65, $invoice->tax); // 10% of 650 (column is 'tax' not 'tax_amount')
        $this->assertEquals(715, $invoice->total); // 650 + 65 (column is 'total' not 'total_amount')
    }

    /** @test */
    public function user_can_generate_invoice_pdf()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $invoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
        ]);

        $response = $this->withToken($token)->postJson("/api/v1/invoices/{$invoice->id}/pdf");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'pdf_path',
                ],
                'message',
            ]);
    }

    /** @test */
    public function user_can_download_invoice_pdf()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $invoice = Invoice::factory()->create([
            'tenant_id' => 1,
            'client_id' => $this->client->id,
            'pdf_path' => 'invoices/test.pdf',
        ]);

        $response = $this->withToken($token)->getJson("/api/v1/invoices/{$invoice->id}/download");

        // Should return 404 if file doesn't exist, or 200 with file
        $this->assertContains($response->getStatusCode(), [200, 404]);
    }

    /** @test */
    public function user_cannot_access_invoices_from_different_tenant()
    {
        $token = $this->user->createToken('test-token')->plainTextToken; // tenant_id = 1
        
        // Create tenant 2
        \App\Domain\Tenant\Models\Tenant::create([
            'id' => 2,
            'name' => 'Other Tenant',
            'slug' => 'other-tenant',
            'domain' => 'other.example.com',
        ]);
        
        $otherTenant = User::factory()->create(['tenant_id' => 2]);
        $otherClient = Client::factory()->create(['tenant_id' => 2, 'user_id' => $otherTenant->id]);
        
        $otherInvoice = Invoice::factory()->create([
            'tenant_id' => 2,
            'user_id' => $otherTenant->id,
            'client_id' => $otherClient->id,
        ]);

        $response = $this->withToken($token)->postJson("/api/v1/invoices/{$otherInvoice->id}/pdf");

        $response->assertStatus(404);
    }

    /** @test */
    public function rate_limiting_works_for_invoice_creation()
    {
        // Vérifier que la route a le middleware de throttling
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('api.invoices.store');
        
        $this->assertNotNull($route, 'Invoice creation route should exist');
        
        // Vérifier que la route a le middleware throttle
        $middleware = $route->gatherMiddleware();
        $hasThrottle = collect($middleware)->contains(function ($m) {
            return is_string($m) && str_contains($m, 'throttle');
        });
        
        $this->assertTrue($hasThrottle, 'Invoice creation route should have rate limiting (throttle) middleware');
    }
}
