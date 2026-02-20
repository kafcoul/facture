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
     * @OA\Post(
     *     path="/v1/payments",
     *     summary="Initier un paiement pour une facture",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"invoice_id","amount","gateway","currency","return_url"},
     *             @OA\Property(property="invoice_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", example=150000),
     *             @OA\Property(property="gateway", type="string", enum={"stripe","paypal","wave","orange_money","mtn_momo"}, example="orange_money"),
     *             @OA\Property(property="currency", type="string", enum={"XOF","USD","EUR"}, example="XOF"),
     *             @OA\Property(property="payment_method", type="string", example="mobile_money", nullable=true),
     *             @OA\Property(property="return_url", type="string", format="url", example="https://invoice-saas.com/payment/callback"),
     *             @OA\Property(property="metadata", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Paiement initié — redirigez vers redirect_url",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment initiated successfully"),
     *             @OA\Property(property="redirect_url", type="string", example="https://pay.orange.ci/checkout/abc123"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", example=150000),
     *                 @OA\Property(property="gateway", type="string", example="orange_money"),
     *                 @OA\Property(property="status", type="string", example="pending")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur lors du traitement du paiement"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
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
     * @OA\Post(
     *     path="/v1/payments/{id}/confirm",
     *     summary="Confirmer un paiement (callback depuis la passerelle)",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du paiement",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             description="Données de confirmation renvoyées par la passerelle",
     *             @OA\Property(property="transaction_id", type="string", example="txn_abc123"),
     *             @OA\Property(property="status", type="string", example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paiement confirmé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment confirmed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", example=150000),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="invoice", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Échec de la confirmation"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
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
