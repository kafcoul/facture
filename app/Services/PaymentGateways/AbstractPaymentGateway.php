<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Domain\Invoice\Models\Invoice;
use Illuminate\Support\Facades\Config;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected array $config;
    protected string $gatewayKey;

    public function __construct(string $gatewayKey)
    {
        $this->gatewayKey = $gatewayKey;
        $this->config = Config::get("payments.gateways.{$gatewayKey}", []);
    }

    public function isEnabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? false);
    }

    public function getSupportedCountries(): array
    {
        return $this->config['countries'] ?? [];
    }

    public function getSupportedCurrencies(): array
    {
        return $this->config['currencies'] ?? [];
    }

    protected function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    protected function validatePaymentData(Invoice $invoice, array $customerData): void
    {
        if ($invoice->status === 'paid') {
            throw new \InvalidArgumentException('Invoice already paid');
        }

        if ($invoice->total <= 0) {
            throw new \InvalidArgumentException('Invalid invoice amount');
        }

        if (!isset($customerData['email'])) {
            throw new \InvalidArgumentException('Customer email is required');
        }
    }

    protected function formatAmount(float $amount, string $currency = 'USD'): int
    {
        // Most African gateways use the smallest currency unit
        // XOF, XAF don't have decimal places
        if (in_array($currency, ['XOF', 'XAF', 'GNF', 'RWF', 'UGX'])) {
            return (int) $amount;
        }

        // Others use cents
        return (int) ($amount * 100);
    }

    /**
     * Default refund implementation — override in concrete gateways
     */
    public function refund(string $transactionId, float $amount = 0, string $reason = ''): array
    {
        return [
            'success' => false,
            'refund_id' => null,
            'message' => "Refund not supported for gateway: {$this->getName()}",
        ];
    }

    abstract public function getName(): string;
}
