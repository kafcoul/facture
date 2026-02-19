<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Repositories\PaymentRepositoryInterface;
use Illuminate\Support\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    /**
     * @var Payment
     */
    protected Payment $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    /**
     * Trouver un paiement par ID
     */
    public function findById(int $id): ?Payment
    {
        return $this->model->find($id);
    }

    /**
     * Trouver un paiement par transaction ID
     */
    public function findByTransactionId(string $transactionId): ?Payment
    {
        return $this->model->where('transaction_id', $transactionId)->first();
    }

    /**
     * Récupérer tous les paiements d'une facture
     */
    public function getByInvoiceId(int $invoiceId): Collection
    {
        return $this->model->where('invoice_id', $invoiceId)
                           ->orderBy('created_at', 'desc')
                           ->get();
    }

    /**
     * Créer un nouveau paiement
     */
    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    /**
     * Mettre à jour un paiement
     */
    public function update(Payment $payment, array $data): bool
    {
        return $payment->update($data);
    }

    /**
     * Récupérer les paiements par gateway
     */
    public function getByGateway(string $gateway, int $tenantId): Collection
    {
        return $this->model->where('gateway', $gateway)
                           ->forTenant($tenantId)
                           ->orderBy('created_at', 'desc')
                           ->get();
    }

    /**
     * Calculer le total des paiements pour un tenant
     */
    public function getTotalForTenant(int $tenantId): float
    {
        return (float) $this->model->completed()
                                   ->forTenant($tenantId)
                                   ->sum('amount');
    }

    /**
     * Récupérer les paiements en attente
     */
    public function getPendingForTenant(int $tenantId): Collection
    {
        return $this->model->pending()
                           ->forTenant($tenantId)
                           ->with(['invoice', 'invoice.client'])
                           ->orderBy('created_at', 'desc')
                           ->get();
    }
}
