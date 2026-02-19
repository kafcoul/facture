<?php
namespace App\Services;
use App\Domain\Invoice\Models\Invoice;

class InvoiceNumberService
{
    /**
     * Générer un numéro de facture unique
     * 
     * @param int|null $tenantId
     * @return string
     */
    public function generate(?int $tenantId = null): string
    {
        $query = Invoice::withoutGlobalScopes();
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        $last = $query->orderByDesc('id')->first();
        $next = $last ? $last->id + 1 : 1;
        return 'INV-' . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
