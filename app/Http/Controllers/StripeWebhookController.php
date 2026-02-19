<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $event = $request->all();
        $eventType = data_get($event, 'type', 'unknown');

        // Log webhook
        WebhookLog::log('stripe', $eventType, $event);

        try {
            return match ($eventType) {
                'checkout.session.completed' => $this->handleCheckoutCompleted($event),
                'payment_intent.succeeded' => $this->handlePaymentSucceeded($event),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event),
                'charge.refunded' => $this->handleChargeRefunded($event),
                'charge.dispute.created' => $this->handleDisputeCreated($event),
                default => response()->json(['status' => 'ignored', 'type' => $eventType]),
            };
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing error', [
                'type' => $eventType,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handleCheckoutCompleted(array $event)
    {
        $invoiceId = data_get($event, 'data.object.metadata.invoice_id');
        $invoice = Invoice::find($invoiceId);

        if ($invoice) {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);

            // Update associated payment if exists
            $paymentIntent = data_get($event, 'data.object.payment_intent');
            if ($paymentIntent) {
                Payment::where('transaction_id', $paymentIntent)->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentSucceeded(array $event)
    {
        $paymentIntentId = data_get($event, 'data.object.id');
        $invoiceId = data_get($event, 'data.object.metadata.invoice_id');

        $payment = Payment::where('transaction_id', $paymentIntentId)->first();

        if ($payment && $payment->status !== 'completed') {
            $payment->markAsCompleted();
            $payment->invoice?->update(['status' => 'paid', 'paid_at' => now()]);
        } elseif ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            $invoice?->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentFailed(array $event)
    {
        $paymentIntentId = data_get($event, 'data.object.id');
        $failureMessage = data_get($event, 'data.object.last_payment_error.message', 'Paiement échoué');

        $payment = Payment::where('transaction_id', $paymentIntentId)->first();

        if ($payment && $payment->status === 'pending') {
            $payment->markAsFailed($failureMessage);
        }

        Log::warning('Stripe payment failed', [
            'payment_intent' => $paymentIntentId,
            'reason' => $failureMessage,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function handleChargeRefunded(array $event)
    {
        $paymentIntentId = data_get($event, 'data.object.payment_intent');
        $amountRefunded = data_get($event, 'data.object.amount_refunded', 0);
        $amountTotal = data_get($event, 'data.object.amount', 0);
        $isFullRefund = $amountRefunded >= $amountTotal;

        $payment = Payment::where('transaction_id', $paymentIntentId)->first();

        if ($payment) {
            $payment->update([
                'status' => 'refunded',
                'metadata' => array_merge($payment->metadata ?? [], [
                    'refund_amount' => $amountRefunded / 100,
                    'full_refund' => $isFullRefund,
                    'refunded_at' => now()->toIso8601String(),
                ]),
            ]);

            // If full refund, update invoice status
            if ($isFullRefund && $payment->invoice) {
                $payment->invoice->update(['status' => 'cancelled']);
            }
        }

        Log::info('Stripe charge refunded', [
            'payment_intent' => $paymentIntentId,
            'amount_refunded' => $amountRefunded / 100,
            'full_refund' => $isFullRefund,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function handleDisputeCreated(array $event)
    {
        $paymentIntentId = data_get($event, 'data.object.payment_intent');
        $reason = data_get($event, 'data.object.reason', 'unknown');

        Log::critical('Stripe dispute created', [
            'payment_intent' => $paymentIntentId,
            'reason' => $reason,
            'amount' => data_get($event, 'data.object.amount', 0) / 100,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
