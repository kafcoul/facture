<?php

namespace App\Domain\Invoice\Repositories;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Support\Collection;

interface InvoiceRepositoryInterface
{
    /**
     * Trouver une facture par UUID
     */
    public function findByUuid(string $uuid): ?Invoice;

    /**
     * Trouver une facture par numéro
     */
    public function findByNumber(string $number): ?Invoice;

    /**
     * Récupérer toutes les factures d'un tenant
     */
    public function getAllForTenant(int $tenantId): Collection;

    /**
     * Récupérer les factures impayées
     */
    public function getUnpaidForTenant(int $tenantId): Collection;

    /**
     * Créer une nouvelle facture
     */
    public function create(array $data): Invoice;

    /**
     * Mettre à jour une facture
     */
    public function update(Invoice $invoice, array $data): bool;

    /**
     * Supprimer une facture
     */
    public function delete(Invoice $invoice): bool;

    /**
     * Récupérer les factures par statut
     */
    public function getByStatus(string $status, int $tenantId): Collection;

    /**
     * Récupérer les factures d'un client
     */
    public function getByClientId(int $clientId): Collection;

    /**
     * Calculer le revenu total d'un tenant
     */
    public function getTotalRevenueForTenant(int $tenantId): float;

    /**
     * Récupérer les factures overdue
     */
    public function getOverdueForTenant(int $tenantId): Collection;
}
