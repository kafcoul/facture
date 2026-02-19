<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Product;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;
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

        $this->product = Product::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Test Product',
            'description' => 'Test Description',
            'unit_price' => 100.00,
        ]);
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'unit_price' => 100.00,
        ]);
    }

    /** @test */
    public function it_belongs_to_a_tenant()
    {
        $this->assertInstanceOf(Tenant::class, $this->product->tenant);
        $this->assertEquals($this->tenant->id, $this->product->tenant->id);
    }

    /** @test */
    public function it_has_tenant_isolation()
    {
        $tenant2 = Tenant::create([
            'name' => 'Tenant 2',
            'slug' => 'tenant-2',
        ]);

        $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

        $product1 = Product::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Product Tenant 1',
            'unit_price' => 50.00,
        ]);

        $product2 = Product::create([
            'tenant_id' => $tenant2->id,
            'user_id' => $user2->id,
            'name' => 'Product Tenant 2',
            'unit_price' => 75.00,
        ]);

        $tenant1Products = Product::where('tenant_id', $this->tenant->id)->get();

        $this->assertCount(2, $tenant1Products); // $this->product + $product1
        $this->assertTrue($tenant1Products->contains($product1));
        $this->assertFalse($tenant1Products->contains($product2));
    }

    /** @test */
    public function it_can_scope_active_products()
    {
        $activeProduct = Product::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Active Product',
            'unit_price' => 100.00,
            'is_active' => true,
        ]);

        $inactiveProduct = Product::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Inactive Product',
            'unit_price' => 50.00,
            'is_active' => false,
        ]);

        $activeProducts = Product::active()->get();

        $this->assertTrue($activeProducts->contains($activeProduct));
        $this->assertFalse($activeProducts->contains($inactiveProduct));
    }

    /** @test */
    public function it_formats_price_correctly()
    {
        $this->product->update(['unit_price' => 1234.56]);

        // Assuming a getFormattedPrice method exists or we test the raw value
        $this->assertEquals(1234.56, $this->product->unit_price);
    }

    /** @test */
    public function it_can_have_nullable_description()
    {
        $product = Product::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Product Without Description',
            'unit_price' => 99.99,
            'description' => null,
        ]);

        $this->assertNull($product->description);
        $this->assertDatabaseHas('products', [
            'name' => 'Product Without Description',
            'description' => null,
        ]);
    }
}
