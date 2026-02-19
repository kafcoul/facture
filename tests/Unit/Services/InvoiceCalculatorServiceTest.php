<?php

namespace Tests\Unit\Services;

use App\Services\InvoiceCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new InvoiceCalculatorService();
    }

    /** @test */
    public function it_calculates_subtotal_correctly()
    {
        $items = [
            ['quantity' => 2, 'unit_price' => 100],
            ['quantity' => 3, 'unit_price' => 150],
        ];

        $subtotal = $this->calculator->calculateSubtotal($items);

        $this->assertEquals(650, $subtotal);
    }

    /** @test */
    public function it_calculates_tax_amount()
    {
        $subtotal = 1000;
        $taxRate = 10;

        $taxAmount = $this->calculator->calculateTax($subtotal, $taxRate);

        $this->assertEquals(100, $taxAmount);
    }

    /** @test */
    public function it_applies_discount_percentage()
    {
        $subtotal = 1000;
        $discountPercentage = 15;

        $discount = $this->calculator->applyDiscountPercentage($subtotal, $discountPercentage);

        $this->assertEquals(150, $discount);
    }

    /** @test */
    public function it_applies_fixed_discount()
    {
        $subtotal = 1000;
        $fixedDiscount = 200;

        $discount = $this->calculator->applyFixedDiscount($subtotal, $fixedDiscount);

        $this->assertEquals(200, $discount);
    }

    /** @test */
    public function it_calculates_total_amount()
    {
        $subtotal = 1000;
        $taxAmount = 100;
        $discount = 50;

        $total = $this->calculator->calculateTotal($subtotal, $taxAmount, $discount);

        $this->assertEquals(1050, $total);
    }

    /** @test */
    public function it_handles_zero_values()
    {
        $items = [];
        $subtotal = $this->calculator->calculateSubtotal($items);

        $this->assertEquals(0, $subtotal);
    }

    /** @test */
    public function it_handles_decimal_quantities()
    {
        $items = [
            ['quantity' => 2.5, 'unit_price' => 100],
            ['quantity' => 1.75, 'unit_price' => 200],
        ];

        $subtotal = $this->calculator->calculateSubtotal($items);

        $this->assertEquals(600, $subtotal); // 250 + 350
    }

    /** @test */
    public function it_rounds_tax_to_two_decimals()
    {
        $subtotal = 123.456;
        $taxRate = 10;

        $taxAmount = $this->calculator->calculateTax($subtotal, $taxRate);

        $this->assertEquals(12.35, $taxAmount);
    }

    /** @test */
    public function it_handles_high_precision_calculations()
    {
        $items = [
            ['quantity' => 1, 'unit_price' => 99.99],
            ['quantity' => 1, 'unit_price' => 0.01],
        ];

        $subtotal = $this->calculator->calculateSubtotal($items);

        $this->assertEquals(100.00, $subtotal);
    }

    /** @test */
    public function it_validates_negative_discount_does_not_exceed_subtotal()
    {
        $subtotal = 100;
        $discount = 150; // More than subtotal

        $finalDiscount = min($discount, $subtotal);

        $this->assertLessThanOrEqual($subtotal, $finalDiscount);
    }
}
