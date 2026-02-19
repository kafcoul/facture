<?php

namespace App\Application\DTOs;

/**
 * Data Transfer Object pour la création d'une facture
 * Immutable, validation stricte des données
 */
class CreateInvoiceDTO
{
    public function __construct(
        public readonly int $tenantId,
        public readonly int $userId,
        public readonly int $clientId,
        public readonly string $type,
        public readonly array $items,
        public readonly ?string $dueDate = null,
        public readonly ?string $issuedAt = null,
        public readonly ?float $taxRate = null,
        public readonly ?float $discount = null,
        public readonly ?string $notes = null,
        public readonly ?string $terms = null,
        public readonly string $currency = 'XOF',
    ) {}

    /**
     * Créer un DTO à partir d'un tableau de données
     */
    public static function fromArray(array $data): self
    {
        return new self(
            tenantId: $data['tenant_id'],
            userId: $data['user_id'],
            clientId: $data['client_id'],
            type: $data['type'] ?? 'invoice',
            items: $data['items'] ?? [],
            dueDate: $data['due_date'] ?? null,
            issuedAt: $data['issued_at'] ?? null,
            taxRate: $data['tax_rate'] ?? null,
            discount: $data['discount'] ?? null,
            notes: $data['notes'] ?? null,
            terms: $data['terms'] ?? null,
            currency: $data['currency'] ?? 'XOF',
        );
    }

    /**
     * Valider les données
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->items)) {
            $errors[] = 'Invoice must have at least one item';
        }

        foreach ($this->items as $index => $item) {
            if (!isset($item['description']) || empty($item['description'])) {
                $errors[] = "Item #{$index}: description is required";
            }
            if (!isset($item['quantity']) || $item['quantity'] <= 0) {
                $errors[] = "Item #{$index}: quantity must be greater than 0";
            }
            if (!isset($item['unit_price']) || $item['unit_price'] < 0) {
                $errors[] = "Item #{$index}: unit_price must be non-negative";
            }
        }

        return $errors;
    }

    /**
     * Convertir en tableau
     */
    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'client_id' => $this->clientId,
            'type' => $this->type,
            'items' => $this->items,
            'due_date' => $this->dueDate,
            'issued_at' => $this->issuedAt,
            'tax_rate' => $this->taxRate,
            'discount' => $this->discount,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'currency' => $this->currency,
        ];
    }
}
