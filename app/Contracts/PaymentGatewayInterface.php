<?php

namespace App\Contracts;

use App\Domain\Invoice\Models\Invoice;

interface PaymentGatewayInterface
{
    /**
     * Initialize a payment transaction
     *
     * @param Invoice $invoice
     * @param array $customerData
     * @return array
     */
    public function createPayment(Invoice $invoice, array $customerData): array;

    /**
     * Verify a payment status
     *
     * @param string $reference
     * @return array
     */
    public function verifyPayment(string $reference): array;

    /**
     * Process a webhook notification
     *
     * @param array $payload
     * @return bool
     */
    public function handleWebhook(array $payload): bool;

    /**
     * Refund a payment (full or partial)
     *
     * @param string $transactionId
     * @param float $amount Amount to refund (0 = full refund)
     * @param string $reason
     * @return array ['success' => bool, 'refund_id' => string|null, 'message' => string]
     */
    public function refund(string $transactionId, float $amount = 0, string $reason = ''): array;

    /**
     * Get the gateway name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check if gateway is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Get supported countries
     *
     * @return array
     */
    public function getSupportedCountries(): array;

    /**
     * Get supported currencies
     *
     * @return array
     */
    public function getSupportedCurrencies(): array;
}
