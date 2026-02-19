<?php

namespace App\Services;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected string $gateway;
    protected array $config;

    public function __construct(?string $gateway = null)
    {
        $this->gateway = $gateway ?? Config::get('payments.default');
        $this->config = Config::get("payments.gateways.{$this->gateway}", []);
    }

    /**
     * Get available payment gateways for a specific country
     */
    public static function getAvailableGateways(?string $countryCode = null): array
    {
        $gateways = Config::get('payments.gateways', []);
        $available = [];

        foreach ($gateways as $key => $config) {
            if (!($config['enabled'] ?? false)) {
                continue;
            }

            if ($countryCode && isset($config['countries']) && $config['countries'] !== ['*']) {
                if (!in_array($countryCode, $config['countries'])) {
                    continue;
                }
            }

            $available[$key] = [
                'name' => self::getGatewayName($key),
                'key' => $key,
                'countries' => $config['countries'] ?? [],
            ];
        }

        return $available;
    }

    /**
     * Get mobile money providers for a country
     */
    public static function getMobileMoneyProviders(string $countryCode): array
    {
        return Config::get("payments.mobile_money.{$countryCode}", []);
    }

    /**
     * Get supported currency for a country
     */
    public static function getCurrency(string $countryCode): string
    {
        $currencies = Config::get('payments.currencies', []);
        
        foreach ($currencies as $currency => $countries) {
            if ($countries === ['*'] || in_array($countryCode, $countries)) {
                return $currency;
            }
        }

        return 'USD'; // Fallback
    }

    /**
     * Initier un paiement (method pour Use Case)
     * 
     * @param string $gateway Gateway name
     * @param float $amount Amount to charge
     * @param string $currency Currency code
     * @param int $invoiceId Invoice ID
     * @param string $clientEmail Client email
     * @param string|null $returnUrl Return URL after payment
     * @param array $metadata Additional metadata
     * @return array ['transaction_id' => string, 'redirect_url' => string|null]
     */
    public function initiatePayment(
        string $gateway,
        float $amount,
        string $currency,
        int $invoiceId,
        string $clientEmail,
        ?string $returnUrl = null,
        array $metadata = []
    ): array {
        $this->gateway = $gateway;
        $this->config = Config::get("payments.gateways.{$gateway}", []);

        if (!($this->config['enabled'] ?? false)) {
            throw new \Exception("Gateway {$gateway} is not enabled");
        }

        // Charger la facture
        $invoice = \App\Domain\Invoice\Models\Invoice::findOrFail($invoiceId);

        $customerData = [
            'email' => $clientEmail,
            'name' => $invoice->client->name ?? '',
        ];

        $result = $this->createPayment($invoice, $customerData);

        return [
            'transaction_id' => $result['reference'] ?? null,
            'redirect_url' => $result['authorization_url'] ?? $result['payment_url'] ?? $result['wave_launch_url'] ?? null,
            'gateway' => $gateway,
            'raw_response' => $result,
        ];
    }

    /**
     * Create a payment intent/transaction
     */
    public function createPayment(Invoice $invoice, array $customerData = []): array
    {
        if (!$this->config['enabled'] ?? false) {
            throw new \Exception("Gateway {$this->gateway} is not enabled");
        }

        return match ($this->gateway) {
            'stripe' => $this->createStripePayment($invoice, $customerData),
            'paystack' => $this->createPaystackPayment($invoice, $customerData),
            'flutterwave' => $this->createFlutterwavePayment($invoice, $customerData),
            'wave' => $this->createWavePayment($invoice, $customerData),
            'mpesa' => $this->createMpesaPayment($invoice, $customerData),
            'fedapay' => $this->createFedapayPayment($invoice, $customerData),
            'kkiapay' => $this->createKkiapayPayment($invoice, $customerData),
            'cinetpay' => $this->createCinetpayPayment($invoice, $customerData),
            default => throw new \Exception("Gateway {$this->gateway} not implemented"),
        };
    }

    /**
     * Verify a payment (overload pour accepter gateway et data)
     */
    public function verifyPayment(string $gatewayOrReference, ?array $data = null): array|bool
    {
        // Si appelé avec 2 arguments (depuis Use Case)
        if ($data !== null) {
            $this->gateway = $gatewayOrReference;
            $this->config = Config::get("payments.gateways.{$gatewayOrReference}", []);
            $reference = $data['reference'] ?? $data['transaction_id'] ?? '';
            
            $result = $this->verifyPaymentByGateway($reference);
            return $result['status'] === 'success';
        }

        // Si appelé avec 1 argument (legacy)
        return $this->verifyPaymentByGateway($gatewayOrReference);
    }

    /**
     * Verify payment by reference
     */
    protected function verifyPaymentByGateway(string $reference): array
    {
        return match ($this->gateway) {
            'stripe' => $this->verifyStripePayment($reference),
            'paystack' => $this->verifyPaystackPayment($reference),
            'flutterwave' => $this->verifyFlutterwavePayment($reference),
            'wave' => $this->verifyWavePayment($reference),
            'mpesa' => $this->verifyMpesaPayment($reference),
            'fedapay' => $this->verifyFedapayPayment($reference),
            'kkiapay' => $this->verifyKkiapayPayment($reference),
            'cinetpay' => $this->verifyCinetpayPayment($reference),
            default => throw new \Exception("Gateway {$this->gateway} not implemented"),
        };
    }

    // ==================== STRIPE ====================
    protected function createStripePayment(Invoice $invoice, array $customerData): array
    {
        \Stripe\Stripe::setApiKey($this->config['secret']);

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => (int)($invoice->total * 100),
            'currency' => strtolower($invoice->currency ?? 'eur'),
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
            ],
        ]);

        return [
            'reference' => $paymentIntent->id,
            'client_secret' => $paymentIntent->client_secret,
            'gateway' => 'stripe',
        ];
    }

    protected function verifyStripePayment(string $reference): array
    {
        \Stripe\Stripe::setApiKey($this->config['secret']);
        $paymentIntent = \Stripe\PaymentIntent::retrieve($reference);

        return [
            'status' => $paymentIntent->status === 'succeeded' ? 'success' : 'failed',
            'amount' => $paymentIntent->amount / 100,
            'reference' => $reference,
        ];
    }

    // ==================== PAYSTACK ====================
    protected function createPaystackPayment(Invoice $invoice, array $customerData): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->post('https://api.paystack.co/transaction/initialize', [
                'amount' => (int)($invoice->total * 100), // En kobo
                'email' => $customerData['email'] ?? $invoice->client->email,
                'currency' => $invoice->currency ?? 'NGN',
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                ],
                'callback_url' => route('invoices.payment.callback', ['invoice' => $invoice]),
            ]);

        $data = $response->json();

        if (!$data['status']) {
            throw new \Exception($data['message'] ?? 'Paystack initialization failed');
        }

        return [
            'reference' => $data['data']['reference'],
            'authorization_url' => $data['data']['authorization_url'],
            'access_code' => $data['data']['access_code'],
            'gateway' => 'paystack',
        ];
    }

    protected function verifyPaystackPayment(string $reference): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        return [
            'status' => $data['data']['status'] === 'success' ? 'success' : 'failed',
            'amount' => $data['data']['amount'] / 100,
            'reference' => $reference,
        ];
    }

    // ==================== FLUTTERWAVE ====================
    protected function createFlutterwavePayment(Invoice $invoice, array $customerData): array
    {
        $reference = 'INV-' . $invoice->id . '-' . time();

        $response = Http::withToken($this->config['secret_key'])
            ->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $reference,
                'amount' => $invoice->total,
                'currency' => $invoice->currency ?? 'NGN',
                'redirect_url' => route('invoices.payment.callback', ['invoice' => $invoice]),
                'customer' => [
                    'email' => $customerData['email'] ?? $invoice->client->email,
                    'name' => $customerData['name'] ?? $invoice->client->name,
                ],
                'customizations' => [
                    'title' => "Facture {$invoice->number}",
                    'description' => "Paiement de la facture {$invoice->number}",
                ],
            ]);

        $data = $response->json();

        return [
            'reference' => $reference,
            'payment_link' => $data['data']['link'],
            'gateway' => 'flutterwave',
        ];
    }

    protected function verifyFlutterwavePayment(string $reference): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->get("https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref={$reference}");

        $data = $response->json();

        return [
            'status' => $data['data']['status'] === 'successful' ? 'success' : 'failed',
            'amount' => $data['data']['amount'],
            'reference' => $reference,
        ];
    }

    // ==================== WAVE ====================
    protected function createWavePayment(Invoice $invoice, array $customerData): array
    {
        $response = Http::withToken($this->config['api_key'])
            ->post('https://api.wave.com/v1/checkout/sessions', [
                'amount' => (int)($invoice->total),
                'currency' => $invoice->currency ?? 'XOF',
                'error_url' => route('invoices.payment.error', ['invoice' => $invoice]),
                'success_url' => route('invoices.payment.success', ['invoice' => $invoice]),
            ]);

        $data = $response->json();

        return [
            'reference' => $data['id'],
            'wave_launch_url' => $data['wave_launch_url'],
            'gateway' => 'wave',
        ];
    }

    protected function verifyWavePayment(string $reference): array
    {
        $response = Http::withToken($this->config['api_key'])
            ->get("https://api.wave.com/v1/checkout/sessions/{$reference}");

        $data = $response->json();

        return [
            'status' => $data['payment_status'] === 'succeeded' ? 'success' : 'failed',
            'amount' => $data['amount'],
            'reference' => $reference,
        ];
    }

    // ==================== M-PESA ====================
    protected function createMpesaPayment(Invoice $invoice, array $customerData): array
    {
        // STK Push implementation
        $timestamp = date('YmdHis');
        $password = base64_encode($this->config['shortcode'] . $this->config['passkey'] . $timestamp);

        $response = Http::withToken($this->getMpesaAccessToken())
            ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $this->config['shortcode'],
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int)$invoice->total,
                'PartyA' => $customerData['phone'] ?? '',
                'PartyB' => $this->config['shortcode'],
                'PhoneNumber' => $customerData['phone'] ?? '',
                'CallBackURL' => route('webhooks.mpesa'),
                'AccountReference' => $invoice->number,
                'TransactionDesc' => "Payment for {$invoice->number}",
            ]);

        $data = $response->json();

        return [
            'reference' => $data['CheckoutRequestID'] ?? '',
            'merchant_request_id' => $data['MerchantRequestID'] ?? '',
            'gateway' => 'mpesa',
        ];
    }

    protected function getMpesaAccessToken(): string
    {
        $response = Http::withBasicAuth(
            $this->config['consumer_key'],
            $this->config['consumer_secret']
        )->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        return $response->json()['access_token'];
    }

    protected function verifyMpesaPayment(string $reference): array
    {
        // M-Pesa verification via callback
        return [
            'status' => 'pending',
            'reference' => $reference,
        ];
    }

    // ==================== FEDAPAY ====================
    protected function createFedapayPayment(Invoice $invoice, array $customerData): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->post('https://api.fedapay.com/v1/transactions', [
                'description' => "Facture {$invoice->number}",
                'amount' => (int)$invoice->total,
                'currency' => ['iso' => $invoice->currency ?? 'XOF'],
                'callback_url' => route('invoices.payment.callback', ['invoice' => $invoice]),
                'customer' => [
                    'firstname' => $customerData['name'] ?? $invoice->client->name,
                    'email' => $customerData['email'] ?? $invoice->client->email,
                ],
            ]);

        $data = $response->json();

        return [
            'reference' => $data['v1/transaction']['id'],
            'token' => $data['v1/transaction']['token'],
            'payment_url' => "https://checkout.fedapay.com/{$data['v1/transaction']['token']}",
            'gateway' => 'fedapay',
        ];
    }

    protected function verifyFedapayPayment(string $reference): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->get("https://api.fedapay.com/v1/transactions/{$reference}");

        $data = $response->json();

        return [
            'status' => $data['v1/transaction']['status'] === 'approved' ? 'success' : 'failed',
            'amount' => $data['v1/transaction']['amount'],
            'reference' => $reference,
        ];
    }

    // ==================== KKIAPAY ====================
    protected function createKkiapayPayment(Invoice $invoice, array $customerData): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->config['public_key'],
        ])->post('https://api.kkiapay.me/api/v1/transactions/initialize', [
            'amount' => (int)$invoice->total,
            'reason' => "Facture {$invoice->number}",
            'callback' => route('webhooks.kkiapay'),
            'data' => [
                'invoice_id' => $invoice->id,
            ],
        ]);

        $data = $response->json();

        return [
            'reference' => $data['transactionId'],
            'gateway' => 'kkiapay',
        ];
    }

    protected function verifyKkiapayPayment(string $reference): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->config['private_key'],
        ])->get("https://api.kkiapay.me/api/v1/transactions/{$reference}");

        $data = $response->json();

        return [
            'status' => $data['status'] === 'SUCCESS' ? 'success' : 'failed',
            'amount' => $data['amount'],
            'reference' => $reference,
        ];
    }

    // ==================== CINETPAY ====================
    protected function createCinetpayPayment(Invoice $invoice, array $customerData): array
    {
        $transId = 'INV-' . $invoice->id . '-' . time();

        $response = Http::post('https://api-checkout.cinetpay.com/v2/payment', [
            'apikey' => $this->config['api_key'],
            'site_id' => $this->config['site_id'],
            'transaction_id' => $transId,
            'amount' => (int)$invoice->total,
            'currency' => $invoice->currency ?? 'XOF',
            'description' => "Facture {$invoice->number}",
            'return_url' => route('invoices.payment.callback', ['invoice' => $invoice]),
            'notify_url' => route('webhooks.cinetpay'),
            'customer_name' => $customerData['name'] ?? $invoice->client->name,
            'customer_email' => $customerData['email'] ?? $invoice->client->email,
        ]);

        $data = $response->json();

        return [
            'reference' => $transId,
            'payment_url' => $data['data']['payment_url'],
            'payment_token' => $data['data']['payment_token'],
            'gateway' => 'cinetpay',
        ];
    }

    protected function verifyCinetpayPayment(string $reference): array
    {
        $response = Http::post('https://api-checkout.cinetpay.com/v2/payment/check', [
            'apikey' => $this->config['api_key'],
            'site_id' => $this->config['site_id'],
            'transaction_id' => $reference,
        ]);

        $data = $response->json();

        return [
            'status' => $data['data']['status'] === 'ACCEPTED' ? 'success' : 'failed',
            'amount' => $data['data']['amount'],
            'reference' => $reference,
        ];
    }

    /**
     * Get human-readable gateway name
     */
    protected static function getGatewayName(string $key): string
    {
        return match ($key) {
            'stripe' => 'Stripe',
            'paystack' => 'Paystack',
            'flutterwave' => 'Flutterwave',
            'wave' => 'Wave',
            'orange_money' => 'Orange Money',
            'mtn_momo' => 'MTN Mobile Money',
            'airtel_money' => 'Airtel Money',
            'moov_money' => 'Moov Money',
            'mpesa' => 'M-Pesa',
            'chipper' => 'Chipper Cash',
            'dpo' => 'DPO PayGate',
            'fedapay' => 'FedaPay',
            'kkiapay' => 'Kkiapay',
            'cinetpay' => 'CinetPay',
            'paydunya' => 'PayDunya',
            default => ucfirst(str_replace('_', ' ', $key)),
        };
    }
}
