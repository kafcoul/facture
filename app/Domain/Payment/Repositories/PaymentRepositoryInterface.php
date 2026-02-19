<?php

namespace App\Domain\Payment\Repositories;

use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface
{
    /**
     * Trouver un paiement par ID
     */
    public function findById(int $id): ?Payment;

    /**
     * Trouver un paiement par transaction ID
     */
    public function findByTransactionId(string $transactionId): ?Payment;

    /**
     * Récupérer tous les paiements d'une facture
     */
    public function getByInvoiceId(int $invoiceId): Collection;

    /**
     * Créer un nouveau paiement
     */
    public function create(array $data): Payment;

    /**
     * Mettre à jour un paiement
     */
    public function update(Payment $payment, array $data): bool;

    /**
     * Récupérer les paiements par gateway
     */
    public function getByGateway(string $gateway, int $tenantId): Collection;

    /**
     * Calculer le total des paiements pour un tenant
     */
    public function getTotalForTenant(int $tenantId): float;

    /**
     * Récupérer les paiements en attente
     */
    public function getPendingForTenant(int $tenantId): Collection;
}
