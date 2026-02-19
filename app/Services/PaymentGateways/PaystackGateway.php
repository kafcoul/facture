<?php

namespace App\Services\PaymentGateways;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackGateway extends AbstractPaymentGateway
{
    private const API_BASE_URL = 'https://api.paystack.co';

    public function getName(): string
    {
        return 'Paystack';
    }

    public function createPayment(Invoice $invoice, array $customerData): array
    {
        $this->validatePaymentData($invoice, $customerData);

        try {
            $response = Http::withToken($this->getConfig('secret_key'))
                ->post(self::API_BASE_URL . '/transaction/initialize', [
                    'amount' => $this->formatAmount($invoice->total, $invoice->currency ?? 'NGN'),
                    'email' => $customerData['email'],
                    'currency' => $invoice->currency ?? 'NGN',
                    'metadata' => [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->number,
                        'tenant_id' => $invoice->tenant_id ?? null,
                    ],
                    'callback_url' => route('invoices.payment.callback', ['uuid' => $invoice->uuid]),
                ]);

            $data = $response->json();

            if (!($data['status'] ?? false)) {
                throw new \RuntimeException($data['message'] ?? 'Paystack initialization failed');
            }

            return [
                'success' => true,
                'reference' => $data['data']['reference'],
                'authorization_url' => $data['data']['authorization_url'],
                'access_code' => $data['data']['access_code'],
                'gateway' => 'paystack',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack payment creation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to create Paystack payment: ' . $e->getMessage());
        }
    }

    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withToken($this->getConfig('secret_key'))
                ->get(self::API_BASE_URL . "/transaction/verify/{$reference}");

            $data = $response->json();

            if (!($data['status'] ?? false)) {
                throw new \RuntimeException('Verification failed');
            }

            return [
                'status' => $data['data']['status'] === 'success' ? 'success' : 'failed',
                'amount' => $data['data']['amount'] / 100,
                'currency' => $data['data']['currency'],
                'reference' => $reference,
                'metadata' => $data['data']['metadata'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Paystack payment verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to verify Paystack payment: ' . $e->getMessage());
        }
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
            $body = json_encode($payload);
            $hash = hash_hmac('sha512', $body, $this->getConfig('webhook_secret'));

            if (!hash_equals($hash, $signature)) {
                Log::warning('Invalid Paystack webhook signature');
                return false;
            }

            $event = $payload['event'] ?? '';

            return $event === 'charge.success';
        } catch (\Exception $e) {
            Log::error('Paystack webhook handling failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function refund(string $transactionId, float $amount = 0, string $reason = ''): array
    {
        try {
            $params = [
                'transaction' => $transactionId,
            ];

            if ($amount > 0) {
                $params['amount'] = (int) ($amount * 100); // Paystack uses kobo
            }

            if ($reason) {
                $params['merchant_note'] = $reason;
            }

            $response = Http::withToken($this->getConfig('secret_key'))
                ->post(self::API_BASE_URL . '/refund', $params);

            $data = $response->json();

            if ($data['status'] ?? false) {
                return [
                    'success' => true,
                    'refund_id' => (string) ($data['data']['id'] ?? ''),
                    'message' => 'Remboursement Paystack effectué',
                ];
            }

            return [
                'success' => false,
                'refund_id' => null,
                'message' => $data['message'] ?? 'Échec du remboursement Paystack',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack refund failed', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'refund_id' => null,
                'message' => 'Échec du remboursement Paystack : ' . $e->getMessage(),
            ];
        }
    }
}
