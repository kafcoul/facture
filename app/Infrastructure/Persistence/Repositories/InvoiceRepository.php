<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * @var Invoice
     */
    protected Invoice $model;

    public function __construct(Invoice $model)
    {
        $this->model = $model;
    }

    /**
     * Trouver une facture par UUID
     */
    public function findByUuid(string $uuid): ?Invoice
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    /**
     * Trouver une facture par numéro
     */
    public function findByNumber(string $number): ?Invoice
    {
        return $this->model->where('number', $number)->first();
    }

    /**
     * Récupérer toutes les factures d'un tenant
     */
    public function getAllForTenant(int $tenantId): Collection
    {
        return $this->model->with(['client', 'items', 'payments'])
                           ->forTenant($tenantId)
                           ->orderBy('created_at', 'desc')
                           ->get();
    }

    /**
     * Récupérer les factures impayées
     */
    public function getUnpaidForTenant(int $tenantId): Collection
    {
        return $this->model->unpaid()
                           ->forTenant($tenantId)
                           ->with(['client', 'items'])
                           ->orderBy('due_date', 'asc')
                           ->get();
    }

    /**
     * Créer une nouvelle facture
     */
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoice = $this->model->create($data);
            
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $invoice->items()->create($item);
                }
            }
            
            return $invoice->load(['client', 'items']);
        });
    }

    /**
     * Mettre à jour une facture
     */
    public function update(Invoice $invoice, array $data): bool
    {
        return DB::transaction(function () use ($invoice, $data) {
            $updated = $invoice->update($data);
            
            if (isset($data['items']) && is_array($data['items'])) {
                // Supprimer les anciens items
                $invoice->items()->delete();
                
                // Créer les nouveaux items
                foreach ($data['items'] as $item) {
                    $invoice->items()->create($item);
                }
            }
            
            return $updated;
        });
    }

    /**
     * Supprimer une facture
     */
    public function delete(Invoice $invoice): bool
    {
        return DB::transaction(function () use ($invoice) {
            // Supprimer les items et paiements associés
            $invoice->items()->delete();
            $invoice->payments()->delete();
            
            return $invoice->delete();
        });
    }

    /**
     * Récupérer les factures par statut
     */
    public function getByStatus(string $status, int $tenantId): Collection
    {
        return $this->model->where('status', $status)
                           ->forTenant($tenantId)
                           ->with(['client', 'items'])
                           ->orderBy('created_at', 'desc')
                           ->get();
    }

    /**
     * Récupérer les factures d'un client
     */
    public function getByClientId(int $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)
                           ->with(['items', 'payments'])
                           ->orderBy('created_at', 'desc')
                           ->get();
    }

    /**
     * Calculer le revenu total d'un tenant
     */
    public function getTotalRevenueForTenant(int $tenantId): float
    {
        return (float) $this->model->forTenant($tenantId)
                                   ->where('status', 'paid')
                                   ->sum('total');
    }

    /**
     * Récupérer les factures overdue
     */
    public function getOverdueForTenant(int $tenantId): Collection
    {
        return $this->model->overdue()
                           ->forTenant($tenantId)
                           ->with(['client'])
                           ->orderBy('due_date', 'asc')
                           ->get();
    }
}
