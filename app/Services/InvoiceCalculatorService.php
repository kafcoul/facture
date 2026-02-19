<?php

namespace App\Services;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Support\Facades\Log;

class InvoiceCalculatorService
{
    /**
     * Calculate invoice totals
     *
     * @param Invoice $invoice
     * @return Invoice
     */
    public function calculate(Invoice $invoice): Invoice
    {
        $subtotal = 0;
        $tax = 0;
        $discount = 0;

        foreach ($invoice->items as $item) {
            // Calculate item total
            $itemTotal = $item->quantity * $item->unit_price;
            
            // Apply item discount
            $itemDiscount = $item->discount ?? 0;
            $itemTotal -= $itemDiscount;
            
            // Calculate item tax
            $itemTax = $itemTotal * ($item->tax_rate / 100);
            
            $subtotal += $itemTotal;
            $tax += $itemTax;
            $discount += $itemDiscount;
            
            // Update item total
            $item->update(['total' => $itemTotal]);
        }

        // Apply invoice-level discount if any
        if ($invoice->discount > 0) {
            $discount += $invoice->discount;
            $subtotal -= $invoice->discount;
        }

        // Calculate total
        $total = $subtotal + $tax;

        // Update invoice
        $invoice->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
        ]);

        Log::info('Invoice calculated', [
            'invoice_id' => $invoice->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);

        return $invoice->fresh();
    }

    /**
     * Legacy support
     */
    public static function compute(Invoice $invoice): Invoice
    {
        return (new self())->calculate($invoice);
    }

    /**
     * Calculate subtotal from items array
     *
     * @param array $items
     * @return float
     */
    public function calculateSubtotal(array $items): float
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            // Support both 'quantity' and 'qty' for flexibility
            $quantity = $item['qty'] ?? $item['quantity'] ?? 0;
            $unitPrice = $item['unit_price'] ?? 0;
            $subtotal += $quantity * $unitPrice;
        }
        
        return round($subtotal, 2);
    }

    /**
     * Calculate tax amount
     *
     * @param float $subtotal
     * @param float $taxRate
     * @return float
     */
    public function calculateTax(float $subtotal, float $taxRate): float
    {
        return round($subtotal * ($taxRate / 100), 2);
    }

    /**
     * Apply discount percentage
     *
     * @param float $subtotal
     * @param float $discountPercentage
     * @return float
     */
    public function applyDiscountPercentage(float $subtotal, float $discountPercentage): float
    {
        return round($subtotal * ($discountPercentage / 100), 2);
    }

    /**
     * Apply fixed discount
     *
     * @param float $subtotal
     * @param float $fixedDiscount
     * @return float
     */
    public function applyFixedDiscount(float $subtotal, float $fixedDiscount): float
    {
        return min($fixedDiscount, $subtotal);
    }

    /**
     * Calculate total amount
     *
     * @param float $subtotal
     * @param float $taxAmount
     * @param float $discount
     * @return float
     */
    public function calculateTotal(float $subtotal, float $taxAmount, float $discount): float
    {
        return round($subtotal + $taxAmount - $discount, 2);
    }
}
