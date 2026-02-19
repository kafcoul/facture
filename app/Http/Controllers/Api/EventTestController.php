<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Invoice\CreateInvoiceUseCase;
use App\Application\DTOs\CreateInvoiceDTO;
use App\Domain\Invoice\Events\InvoiceCreated;
use App\Domain\Invoice\Events\InvoiceOverdue;
use App\Domain\Payment\Events\PaymentReceived;
use App\Domain\Payment\Events\PaymentFailed;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Controller de test pour les événements
 * 
 * À SUPPRIMER EN PRODUCTION
 */
class EventTestController extends Controller
{
    /**
     * Tester l'événement InvoiceCreated
     */
    public function testInvoiceCreated(): JsonResponse
    {
        $invoice = Invoice::with(['client', 'items'])->first();

        if (!$invoice) {
            return response()->json([
                'error' => 'No invoice found in database',
            ], 404);
        }

        Log::info('Testing InvoiceCreated event', [
            'invoice_id' => $invoice->id,
        ]);

        // Dispatch l'événement
        event(new InvoiceCreated($invoice));

        return response()->json([
            'message' => 'Event InvoiceCreated dispatched successfully',
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'total' => $invoice->total,
                'client' => $invoice->client->name,
            ],
            'listeners' => [
                'SendInvoiceNotification',
                'GenerateInvoicePdf',
            ],
            'next_step' => 'Check logs or run: php artisan queue:work redis --once',
        ]);
    }

    /**
     * Tester l'événement InvoiceOverdue
     */
    public function testInvoiceOverdue(): JsonResponse
    {
        $invoice = Invoice::where('status', 'pending')
            ->where('due_date', '<', now())
            ->first();

        if (!$invoice) {
            return response()->json([
                'error' => 'No overdue invoice found',
                'suggestion' => 'Create an invoice with past due_date',
            ], 404);
        }

        $daysOverdue = now()->diffInDays($invoice->due_date);

        event(new InvoiceOverdue($invoice, $daysOverdue));

        return response()->json([
            'message' => 'Event InvoiceOverdue dispatched',
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'due_date' => $invoice->due_date,
                'days_overdue' => $daysOverdue,
            ],
            'listener' => 'SendOverdueReminder',
        ]);
    }

    /**
     * Tester l'événement PaymentReceived
     */
    public function testPaymentReceived(): JsonResponse
    {
        $payment = Payment::with(['invoice', 'invoice.client'])
            ->where('status', 'completed')
            ->first();

        if (!$payment) {
            return response()->json([
                'error' => 'No completed payment found',
            ], 404);
        }

        event(new PaymentReceived($payment));

        return response()->json([
            'message' => 'Event PaymentReceived dispatched',
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'gateway' => $payment->gateway,
                'invoice_number' => $payment->invoice->number,
            ],
            'listeners' => [
                'LogPaymentEvent',
                'NotifyAccountant (if amount > 1M XOF)',
            ],
        ]);
    }

    /**
     * Tester l'événement PaymentFailed
     */
    public function testPaymentFailed(): JsonResponse
    {
        $payment = Payment::with('invoice')
            ->where('status', 'failed')
            ->first();

        if (!$payment) {
            // Créer un paiement fictif pour test
            $invoice = Invoice::first();
            if (!$invoice) {
                return response()->json(['error' => 'No invoice found'], 404);
            }

            $payment = Payment::create([
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'amount' => 1000,
                'gateway' => 'stripe',
                'status' => 'failed',
                'currency' => 'XOF',
            ]);
        }

        $reason = 'Test: Insufficient funds';
        event(new PaymentFailed($payment, $reason));

        return response()->json([
            'message' => 'Event PaymentFailed dispatched',
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'gateway' => $payment->gateway,
            ],
            'reason' => $reason,
            'listener' => 'HandlePaymentFailure',
        ]);
    }

    /**
     * Lister les jobs en queue
     */
    public function queueStats(): JsonResponse
    {
        try {
            $redis = app('redis');
            $queueLength = $redis->llen('queues:default');
            
            return response()->json([
                'queue' => 'default',
                'pending_jobs' => $queueLength,
                'connection' => config('queue.default'),
                'command' => 'php artisan queue:work redis --once',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Redis not available',
                'message' => $e->getMessage(),
                'note' => 'Events will be dispatched synchronously',
            ], 500);
        }
    }

    /**
     * Test complet: créer une facture avec événements
     */
    public function testFullWorkflow(CreateInvoiceUseCase $useCase): JsonResponse
    {
        // Récupérer un client existant
        $client = \App\Models\Client::first();
        if (!$client) {
            return response()->json(['error' => 'No client found'], 404);
        }

        // Créer un DTO de test
        $dto = CreateInvoiceDTO::fromArray([
            'tenant_id' => $client->tenant_id,
            'user_id' => $client->tenant->users()->first()->id ?? 1,
            'client_id' => $client->id,
            'type' => 'standard',
            'currency' => 'XOF',
            'tax_rate' => 18,
            'discount' => 0,
            'notes' => 'Test invoice with events',
            'terms' => 'Payment within 30 days',
            'items' => [
                [
                    'description' => 'Test Product 1',
                    'quantity' => 2,
                    'unit_price' => 10000,
                    'tax_rate' => 18,
                ],
                [
                    'description' => 'Test Product 2',
                    'quantity' => 1,
                    'unit_price' => 25000,
                    'tax_rate' => 18,
                ],
            ],
        ]);

        try {
            $invoice = $useCase->execute($dto);

            return response()->json([
                'message' => 'Invoice created successfully with events',
                'invoice' => [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                ],
                'events_dispatched' => [
                    'InvoiceCreated',
                ],
                'listeners_triggered' => [
                    'SendInvoiceNotification (queued)',
                    'GenerateInvoicePdf (queued)',
                ],
                'next_steps' => [
                    '1. Check logs: tail storage/logs/laravel.log',
                    '2. Process queue: php artisan queue:work redis --once',
                    '3. Check queue stats: GET /api/test/events/queue-stats',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create invoice',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
