<?php

namespace App\Application\Services;

/**
 * Service pour calculer les totaux de facture
 * Logique métier pure, sans dépendances externes
 */
class InvoiceCalculatorService
{
    /**
     * Calculer le total d'un item
     */
    public function calculateItemTotal(array $item): float
    {
        $quantity = (float) ($item['quantity'] ?? 0);
        $unitPrice = (float) ($item['unit_price'] ?? 0);
        $taxRate = (float) ($item['tax_rate'] ?? 0);
        $discount = (float) ($item['discount'] ?? 0);

        // Subtotal
        $subtotal = $quantity * $unitPrice;

        // Appliquer la remise
        $discountAmount = $subtotal * ($discount / 100);
        $afterDiscount = $subtotal - $discountAmount;

        // Appliquer la taxe
        $taxAmount = $afterDiscount * ($taxRate / 100);

        return round($afterDiscount + $taxAmount, 2);
    }

    /**
     * Calculer les totaux d'une facture
     */
    public function calculateInvoiceTotals(array $items, ?float $globalTaxRate = null, ?float $globalDiscount = null): array
    {
        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        // Calculer le subtotal de tous les items
        foreach ($items as $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $itemSubtotal = $quantity * $unitPrice;
            $subtotal += $itemSubtotal;

            // Calculer la taxe de l'item
            $taxRate = (float) ($item['tax_rate'] ?? $globalTaxRate ?? 0);
            $discount = (float) ($item['discount'] ?? 0);
            
            $discountAmount = $itemSubtotal * ($discount / 100);
            $afterDiscount = $itemSubtotal - $discountAmount;
            $taxAmount = $afterDiscount * ($taxRate / 100);
            
            $totalTax += $taxAmount;
            $totalDiscount += $discountAmount;
        }

        // Appliquer la remise globale si elle existe
        if ($globalDiscount > 0) {
            $globalDiscountAmount = $subtotal * ($globalDiscount / 100);
            $totalDiscount += $globalDiscountAmount;
            $subtotal -= $globalDiscountAmount;
        }

        // Calculer le total final
        $total = $subtotal + $totalTax;

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($totalTax, 2),
            'discount' => round($totalDiscount, 2),
            'total' => round($total, 2),
        ];
    }

    /**
     * Valider qu'un montant de paiement est valide pour une facture
     */
    public function validatePaymentAmount(float $invoiceTotal, float $paymentAmount, float $alreadyPaid = 0): bool
    {
        $remaining = $invoiceTotal - $alreadyPaid;
        
        // Le montant doit être positif et ne pas dépasser le montant restant
        return $paymentAmount > 0 && $paymentAmount <= $remaining + 0.01; // +0.01 pour erreurs d'arrondi
    }
}
