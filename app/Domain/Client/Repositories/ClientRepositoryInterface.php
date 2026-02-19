<?php

namespace App\Domain\Client\Repositories;

use App\Domain\Client\Models\Client;
use Illuminate\Support\Collection;

interface ClientRepositoryInterface
{
    /**
     * Trouver un client par ID
     */
    public function findById(int $id): ?Client;

    /**
     * Trouver un client par email
     */
    public function findByEmail(string $email): ?Client;

    /**
     * Récupérer tous les clients d'un tenant
     */
    public function getAllForTenant(int $tenantId): Collection;

    /**
     * Créer un nouveau client
     */
    public function create(array $data): Client;

    /**
     * Mettre à jour un client
     */
    public function update(Client $client, array $data): bool;

    /**
     * Supprimer un client
     */
    public function delete(Client $client): bool;

    /**
     * Rechercher des clients
     */
    public function search(string $query, int $tenantId): Collection;

    /**
     * Récupérer les clients avec factures impayées
     */
    public function getWithUnpaidInvoices(int $tenantId): Collection;
}
