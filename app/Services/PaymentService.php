<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Services\PaymentGateways\StripeGateway;
use App\Services\PaymentGateways\PaystackGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class PaymentService
{
    private array $gateways = [];

    public function __construct()
    {
        $this->registerGateways();
    }

    /**
     * Register all available payment gateways
     */
    private function registerGateways(): void
    {
        $this->gateways = [
            'stripe' => StripeGateway::class,
            'paystack' => PaystackGateway::class,
            // Add more gateways here
        ];
    }

    /**
     * Get a payment gateway instance
     *
     * @param string $gateway
     * @return PaymentGatewayInterface
     */
    public function getGateway(string $gateway): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$gateway])) {
            throw new \InvalidArgumentException("Gateway '{$gateway}' not found");
        }

        $gatewayClass = $this->gateways[$gateway];
        $instance = new $gatewayClass($gateway);

        if (!$instance->isEnabled()) {
            throw new \RuntimeException("Gateway '{$gateway}' is not enabled");
        }

        return $instance;
    }

    /**
     * Initialize a payment for an invoice
     *
     * @param Invoice $invoice
     * @param string $gateway
     * @param array $customerData
     * @return array
     */
    public function initializePayment(Invoice $invoice, string $gateway, array $customerData = []): array
    {
        return DB::transaction(function () use ($invoice, $gateway, $customerData) {
            // Validate invoice status
            if ($invoice->status === 'paid') {
                throw new \InvalidArgumentException('Invoice is already paid');
            }

            // Get gateway instance
            $gatewayInstance = $this->getGateway($gateway);

            // Merge customer data with invoice client data
            $customerData = array_merge([
                'email' => $invoice->client->email,
                'name' => $invoice->client->name,
                'phone' => $invoice->client->phone,
            ], $customerData);

            // Create payment with gateway
            $paymentData = $gatewayInstance->createPayment($invoice, $customerData);

            if (!$paymentData['success']) {
                throw new \RuntimeException('Payment initialization failed');
            }

            // Store pending payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total,
                'currency' => $invoice->currency ?? 'USD',
                'gateway' => $gateway,
                'transaction_id' => $paymentData['reference'] ?? null,
                'status' => 'pending',
                'metadata' => $paymentData,
            ]);

            Log::info('Payment initialized', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'gateway' => $gateway,
                'reference' => $paymentData['reference'] ?? null,
            ]);

            return $paymentData;
        });
    }

    /**
     * Verify and complete a payment
     *
     * @param string $reference
     * @param string $gateway
     * @return array
     */
    public function verifyPayment(string $reference, string $gateway): array
    {
        return DB::transaction(function () use ($reference, $gateway) {
            $gatewayInstance = $this->getGateway($gateway);
            $verificationResult = $gatewayInstance->verifyPayment($reference);

            // Find the payment record
            $payment = Payment::where('transaction_id', $reference)->first();

            if (!$payment) {
                throw new \RuntimeException('Payment record not found');
            }

            // Update payment status
            if ($verificationResult['status'] === 'success') {
                $payment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'metadata' => json_encode($verificationResult),
                ]);

                // Update invoice status
                $payment->invoice->update(['status' => 'paid']);

                Log::info('Payment completed', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->invoice_id,
                    'reference' => $reference,
                ]);
            } else {
                $payment->update([
                    'status' => 'failed',
                    'metadata' => json_encode($verificationResult),
                ]);

                Log::warning('Payment failed', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                ]);
            }

            return $verificationResult;
        });
    }

    /**
     * Handle webhook notification
     *
     * @param string $gateway
     * @param array $payload
     * @return bool
     */
    public function handleWebhook(string $gateway, array $payload): bool
    {
        try {
            $gatewayInstance = $this->getGateway($gateway);
            
            if (!$gatewayInstance->handleWebhook($payload)) {
                return false;
            }

            // Extract reference from payload
            $reference = $this->extractReferenceFromWebhook($gateway, $payload);

            if ($reference) {
                $this->verifyPayment($reference, $gateway);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Webhook handling failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Extract payment reference from webhook payload
     *
     * @param string $gateway
     * @param array $payload
     * @return string|null
     */
    private function extractReferenceFromWebhook(string $gateway, array $payload): ?string
    {
        return match ($gateway) {
            'stripe' => $payload['data']['object']['id'] ?? null,
            'paystack' => $payload['data']['reference'] ?? null,
            'flutterwave' => $payload['data']['tx_ref'] ?? null,
            default => null,
        };
    }

    /**
     * Get available gateways for a country
     *
     * @param string|null $countryCode
     * @return array
     */
    public function getAvailableGateways(?string $countryCode = null): array
    {
        $available = [];

        foreach ($this->gateways as $key => $class) {
            try {
                $gateway = new $class($key);

                if (!$gateway->isEnabled()) {
                    continue;
                }

                $supportedCountries = $gateway->getSupportedCountries();

                if ($countryCode && !in_array('*', $supportedCountries) && !in_array($countryCode, $supportedCountries)) {
                    continue;
                }

                $available[$key] = [
                    'name' => $gateway->getName(),
                    'key' => $key,
                    'countries' => $supportedCountries,
                    'currencies' => $gateway->getSupportedCurrencies(),
                ];
            } catch (\Exception $e) {
                Log::warning("Failed to load gateway {$key}", ['error' => $e->getMessage()]);
            }
        }

        return $available;
    }

    /**
     * Get recommended currency for a country
     *
     * @param string $countryCode
     * @return string
     */
    public function getCurrencyForCountry(string $countryCode): string
    {
        $currencies = Config::get('payments.currencies', []);

        foreach ($currencies as $currency => $countries) {
            if ($countries === ['*'] || in_array($countryCode, $countries)) {
                return $currency;
            }
        }

        return 'USD';
    }

    /**
     * Refund a payment (full or partial)
     *
     * @param Payment $payment
     * @param float $amount 0 = full refund
     * @param string $reason
     * @return array
     */
    public function refundPayment(Payment $payment, float $amount = 0, string $reason = ''): array
    {
        if ($payment->status !== 'completed') {
            throw new \InvalidArgumentException('Seuls les paiements complétés peuvent être remboursés');
        }

        if (!$payment->gateway || !$payment->transaction_id) {
            throw new \InvalidArgumentException('Passerelle ou ID de transaction manquant');
        }

        return DB::transaction(function () use ($payment, $amount, $reason) {
            $gatewayInstance = $this->getGateway($payment->gateway);
            $result = $gatewayInstance->refund($payment->transaction_id, $amount, $reason);

            if ($result['success']) {
                $refundAmount = $amount > 0 ? $amount : $payment->amount;
                $isFullRefund = $amount <= 0 || $amount >= (float) $payment->amount;

                $payment->update([
                    'status' => 'refunded',
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'refund_id' => $result['refund_id'],
                        'refund_amount' => $refundAmount,
                        'refund_reason' => $reason,
                        'refunded_at' => now()->toIso8601String(),
                        'full_refund' => $isFullRefund,
                    ]),
                ]);

                // Update invoice status for full refunds
                if ($isFullRefund && $payment->invoice) {
                    $payment->invoice->update(['status' => 'cancelled']);
                }

                Log::info('Payment refunded', [
                    'payment_id' => $payment->id,
                    'refund_id' => $result['refund_id'],
                    'amount' => $refundAmount,
                    'full_refund' => $isFullRefund,
                ]);
            }

            return $result;
        });
    }
}
