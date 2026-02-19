<?php

namespace App\Http\Controllers;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicInvoiceController extends Controller
{
    public function show($uuid)
    {
        $invoice = Invoice::where('uuid', $uuid)->with(['client', 'items.product'])->firstOrFail();
        
        // Use the new multi-gateway view if available, else fallback to original
        if (view()->exists('invoices.public_multi')) {
            return view('invoices.public_multi', compact('invoice'));
        }
        
        return view('invoices.public', compact('invoice'));
    }

    public function pdf($uuid)
    {
        $invoice = Invoice::where('uuid', $uuid)->with(['client', 'items.product'])->firstOrFail();
        
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        
        return $pdf->stream("facture-{$invoice->number}.pdf");
    }

    public function download($uuid)
    {
        $invoice = Invoice::where('uuid', $uuid)->with(['client', 'items.product'])->firstOrFail();
        
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        
        return $pdf->download("facture-{$invoice->number}.pdf");
    }

    /**
     * Initialize payment with selected gateway
     */
    public function initializePayment(Request $request, $uuid)
    {
        try {
            $invoice = Invoice::where('uuid', $uuid)->firstOrFail();

            if ($invoice->status === 'paid') {
                return response()->json(['error' => 'Cette facture est déjà payée'], 400);
            }

            $gateway = $request->input('gateway', config('payments.default'));
            $paymentService = new PaymentGatewayService($gateway);

            $paymentData = $paymentService->createPayment($invoice, [
                'email' => $request->input('email', $invoice->client->email),
                'name' => $request->input('name', $invoice->client->name),
                'phone' => $request->input('phone', $invoice->client->phone),
            ]);

            // Store pending payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total,
                'gateway' => $gateway,
                'transaction_id' => $paymentData['reference'] ?? null,
                'status' => 'pending',
                'currency' => $invoice->currency ?? 'USD',
            ]);

            return response()->json($paymentData);
        } catch (\Exception $e) {
            Log::error('Payment initialization failed', [
                'invoice' => $uuid,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Erreur lors de l\'initialisation du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Payment callback handler
     */
    public function paymentCallback(Request $request, $uuid)
    {
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
        $gateway = $request->input('gateway', config('payments.default'));

        try {
            $paymentService = new PaymentGatewayService($gateway);
            $reference = $request->input('reference') ?? $request->input('transaction_id') ?? $request->input('tx_ref');

            if (!$reference) {
                return redirect()->route('invoices.public', $uuid)
                    ->with('error', 'Référence de paiement manquante');
            }

            $verificationResult = $paymentService->verifyPayment($reference);

            if ($verificationResult['status'] === 'success') {
                $this->updatePaymentStatus($invoice, $reference, 'completed', $gateway);
                return redirect()->route('invoices.payment-success', $uuid);
            }

            return redirect()->route('invoices.payment.error', $uuid)
                ->with('error', 'Le paiement a échoué');

        } catch (\Exception $e) {
            Log::error('Payment callback failed', [
                'invoice' => $uuid,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('invoices.payment.error', $uuid)
                ->with('error', 'Erreur lors de la vérification du paiement');
        }
    }

    public function paymentSuccess($uuid)
    {
        $invoice = Invoice::where('uuid', $uuid)->with(['client', 'payments'])->firstOrFail();
        return view('invoices.payment_success', compact('invoice'));
    }

    public function paymentSuccessAlt($uuid)
    {
        return $this->paymentSuccess($uuid);
    }

    public function paymentError(Request $request, $uuid)
    {
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
        $error = $request->session()->get('error', 'Le paiement a échoué');
        return view('invoices.payment_error', compact('invoice', 'error'));
    }

    /**
     * Stripe legacy support
     */
    public function createPaymentIntent($uuid)
    {
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();

        \Stripe\Stripe::setApiKey(config('payments.gateways.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => (int)($invoice->total * 100),
            'currency' => strtolower($invoice->currency ?? 'eur'),
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
            ],
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function showByHash($hash)
    {
        // Legacy support
        $invoice = Invoice::where('public_hash', $hash)->firstOrFail();
        return redirect()->route('invoices.public', $invoice->uuid);
    }

    // ==================== WEBHOOKS ====================

    public function paystackWebhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $body = $request->getContent();

        if (!$this->verifyPaystackSignature($body, $signature)) {
            return response('Invalid signature', 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'charge.success') {
            $reference = $data['reference'];
            $this->processWebhookPayment($reference, 'paystack');
        }

        return response('Webhook received', 200);
    }

    public function flutterwaveWebhook(Request $request)
    {
        $signature = $request->header('verif-hash');

        if ($signature !== config('payments.gateways.flutterwave.webhook_secret')) {
            return response('Invalid signature', 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'charge.completed' && $data['status'] === 'successful') {
            $reference = $data['tx_ref'];
            $this->processWebhookPayment($reference, 'flutterwave');
        }

        return response('Webhook received', 200);
    }

    public function waveWebhook(Request $request)
    {
        $data = $request->all();

        if (isset($data['payment_status']) && $data['payment_status'] === 'succeeded') {
            $reference = $data['id'];
            $this->processWebhookPayment($reference, 'wave');
        }

        return response('Webhook received', 200);
    }

    public function mpesaWebhook(Request $request)
    {
        $data = $request->all();

        if (isset($data['Body']['stkCallback']['ResultCode']) && $data['Body']['stkCallback']['ResultCode'] == 0) {
            $reference = $data['Body']['stkCallback']['CheckoutRequestID'];
            $this->processWebhookPayment($reference, 'mpesa');
        }

        return response('Webhook received', 200);
    }

    public function fedapayWebhook(Request $request)
    {
        $event = $request->input('entity');

        if (isset($event['status']) && $event['status'] === 'approved') {
            $reference = $event['id'];
            $this->processWebhookPayment($reference, 'fedapay');
        }

        return response('Webhook received', 200);
    }

    public function kkiapayWebhook(Request $request)
    {
        $data = $request->all();

        if (isset($data['status']) && $data['status'] === 'SUCCESS') {
            $reference = $data['transactionId'];
            $this->processWebhookPayment($reference, 'kkiapay');
        }

        return response('Webhook received', 200);
    }

    public function cinetpayWebhook(Request $request)
    {
        $data = $request->all();

        if (isset($data['cpm_result']) && $data['cpm_result'] === '00') {
            $reference = $data['cpm_trans_id'];
            $this->processWebhookPayment($reference, 'cinetpay');
        }

        return response('Webhook received', 200);
    }

    // ==================== HELPERS ====================

    protected function verifyPaystackSignature($body, $signature)
    {
        $hash = hash_hmac('sha512', $body, config('payments.gateways.paystack.webhook_secret'));
        return hash_equals($hash, $signature);
    }

    protected function processWebhookPayment($reference, $gateway)
    {
        $payment = Payment::where('transaction_id', $reference)->first();

        if (!$payment) {
            Log::warning("Payment not found for webhook", ['reference' => $reference, 'gateway' => $gateway]);
            return;
        }

        $this->updatePaymentStatus($payment->invoice, $reference, 'completed', $gateway);
    }

    protected function updatePaymentStatus(Invoice $invoice, $reference, $status, $gateway)
    {
        $payment = Payment::where('invoice_id', $invoice->id)
            ->where('transaction_id', $reference)
            ->first();

        if ($payment) {
            $payment->update(['status' => $status]);
        }

        if ($status === 'completed') {
            $invoice->update(['status' => 'paid']);
        }
    }
}
