<?php

namespace App\Services\PaymentGateways;

use App\Domain\Invoice\Models\Invoice;
use Illuminate\Support\Facades\Log;

class StripeGateway extends AbstractPaymentGateway
{
    public function getName(): string
    {
        return 'Stripe';
    }

    public function createPayment(Invoice $invoice, array $customerData): array
    {
        $this->validatePaymentData($invoice, $customerData);

        try {
            \Stripe\Stripe::setApiKey($this->getConfig('secret'));

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $this->formatAmount($invoice->total, $invoice->currency ?? 'EUR'),
                'currency' => strtolower($invoice->currency ?? 'eur'),
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                    'tenant_id' => $invoice->tenant_id ?? null,
                ],
                'description' => "Invoice #{$invoice->number}",
            ]);

            return [
                'success' => true,
                'reference' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'gateway' => 'stripe',
            ];
        } catch (\Exception $e) {
            Log::error('Stripe payment creation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to create Stripe payment: ' . $e->getMessage());
        }
    }

    public function verifyPayment(string $reference): array
    {
        try {
            \Stripe\Stripe::setApiKey($this->getConfig('secret'));
            $paymentIntent = \Stripe\PaymentIntent::retrieve($reference);

            return [
                'status' => $paymentIntent->status === 'succeeded' ? 'success' : 'failed',
                'amount' => $paymentIntent->amount / 100,
                'currency' => strtoupper($paymentIntent->currency),
                'reference' => $reference,
                'metadata' => $paymentIntent->metadata->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Stripe payment verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to verify Stripe payment: ' . $e->getMessage());
        }
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
            $webhookSecret = $this->getConfig('webhook_secret');

            $event = \Stripe\Webhook::constructEvent(
                json_encode($payload),
                $signature,
                $webhookSecret
            );

            if ($event->type === 'payment_intent.succeeded') {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Stripe webhook handling failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function refund(string $transactionId, float $amount = 0, string $reason = ''): array
    {
        try {
            \Stripe\Stripe::setApiKey($this->getConfig('secret'));

            $params = [
                'payment_intent' => $transactionId,
            ];

            if ($amount > 0) {
                $params['amount'] = (int) ($amount * 100); // Stripe uses cents
            }

            if ($reason) {
                $params['reason'] = 'requested_by_customer';
                $params['metadata'] = ['reason_detail' => $reason];
            }

            $refund = \Stripe\Refund::create($params);

            return [
                'success' => $refund->status === 'succeeded',
                'refund_id' => $refund->id,
                'message' => $refund->status === 'succeeded'
                    ? 'Remboursement Stripe effectué'
                    : "Statut du remboursement : {$refund->status}",
            ];
        } catch (\Exception $e) {
            Log::error('Stripe refund failed', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'refund_id' => null,
                'message' => 'Échec du remboursement Stripe : ' . $e->getMessage(),
            ];
        }
    }
}
