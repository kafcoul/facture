<?php

namespace App\Http\Controllers\Api;

use App\Application\DTOs\ProcessPaymentDTO;
use App\Application\UseCases\Payment\ProcessPaymentUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contrôleur API pour les paiements
 */
class PaymentApiController extends Controller
{
    public function __construct(
        private ProcessPaymentUseCase $processPayment,
    ) {}

    /**
     * Initier un paiement
     * 
     * POST /api/payments
     */
    public function initiatePayment(ProcessPaymentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $dto = ProcessPaymentDTO::fromArray($validated);

            $result = $this->processPayment->execute($dto);

            // Charger les relations
            $result['payment']->load('invoice');

            // Retourner avec API Resource
            return (new PaymentResource($result['payment']))
                ->additional([
                    'message' => 'Payment initiated successfully',
                    'redirect_url' => $result['redirect_url'],
                ])
                ->response()
                ->setStatusCode(201);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirmer un paiement (callback)
     * 
     * POST /api/payments/{id}/confirm
     */
    public function confirmPayment(Request $request, int $id): JsonResponse
    {
        try {
            $payment = $this->processPayment->confirmPayment($id, $request->all());

            // Charger les relations
            $payment->load(['invoice', 'invoice.client']);

            // Retourner avec API Resource
            return (new PaymentResource($payment))
                ->additional([
                    'message' => 'Payment confirmed successfully',
                ])
                ->response();

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
