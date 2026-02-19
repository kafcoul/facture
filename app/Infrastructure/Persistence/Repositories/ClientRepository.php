<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Client\Models\Client;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use Illuminate\Support\Collection;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @var Client
     */
    protected Client $model;

    public function __construct(Client $model)
    {
        $this->model = $model;
    }

    /**
     * Trouver un client par ID
     */
    public function findById(int $id): ?Client
    {
        return $this->model->find($id);
    }

    /**
     * Trouver un client par email
     */
    public function findByEmail(string $email): ?Client
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Récupérer tous les clients d'un tenant
     */
    public function getAllForTenant(int $tenantId): Collection
    {
        return $this->model->forTenant($tenantId)
                           ->orderBy('company', 'asc')
                           ->get();
    }

    /**
     * Créer un nouveau client
     */
    public function create(array $data): Client
    {
        return $this->model->create($data);
    }

    /**
     * Mettre à jour un client
     */
    public function update(Client $client, array $data): bool
    {
        return $client->update($data);
    }

    /**
     * Supprimer un client
     */
    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    /**
     * Rechercher des clients
     */
    public function search(string $query, int $tenantId): Collection
    {
        return $this->model->forTenant($tenantId)
                           ->where(function ($q) use ($query) {
                               $q->where('name', 'like', "%{$query}%")
                                 ->orWhere('company', 'like', "%{$query}%")
                                 ->orWhere('email', 'like', "%{$query}%")
                                 ->orWhere('phone', 'like', "%{$query}%");
                           })
                           ->orderBy('company', 'asc')
                           ->get();
    }

    /**
     * Récupérer les clients avec factures impayées
     */
    public function getWithUnpaidInvoices(int $tenantId): Collection
    {
        return $this->model->forTenant($tenantId)
                           ->whereHas('invoices', function ($query) {
                               $query->where('status', 'pending');
                           })
                           ->with(['invoices' => function ($query) {
                               $query->where('status', 'pending');
                           }])
                           ->get();
    }
}
