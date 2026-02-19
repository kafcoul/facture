<?php

namespace App\Application\DTOs;

/**
 * Data Transfer Object pour le traitement d'un paiement
 */
class ProcessPaymentDTO
{
    public function __construct(
        public readonly int $invoiceId,
        public readonly string $gateway,
        public readonly float $amount,
        public readonly string $currency,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $returnUrl = null,
        public readonly ?string $cancelUrl = null,
        public readonly array $metadata = [],
    ) {}

    /**
     * Créer un DTO à partir d'un tableau
     */
    public static function fromArray(array $data): self
    {
        return new self(
            invoiceId: $data['invoice_id'],
            gateway: $data['gateway'],
            amount: (float) $data['amount'],
            currency: $data['currency'] ?? 'XOF',
            paymentMethod: $data['payment_method'] ?? null,
            returnUrl: $data['return_url'] ?? null,
            cancelUrl: $data['cancel_url'] ?? null,
            metadata: $data['metadata'] ?? [],
        );
    }

    /**
     * Valider les données
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->amount <= 0) {
            $errors[] = 'Amount must be greater than 0';
        }

        $allowedGateways = ['stripe', 'wave', 'paystack', 'flutterwave', 'mtn', 'orange', 'moov'];
        if (!in_array($this->gateway, $allowedGateways)) {
            $errors[] = "Gateway must be one of: " . implode(', ', $allowedGateways);
        }

        return $errors;
    }

    /**
     * Convertir en tableau
     */
    public function toArray(): array
    {
        return [
            'invoice_id' => $this->invoiceId,
            'gateway' => $this->gateway,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->paymentMethod,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'metadata' => $this->metadata,
        ];
    }
}
