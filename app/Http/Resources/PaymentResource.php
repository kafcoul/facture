<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource: Payment
 */
class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'gateway' => $this->gateway,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'payment_method' => $this->payment_method,
            
            // Dates
            'initiated_at' => $this->created_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'failed_at' => $this->failed_at?->toIso8601String(),
            
            // Facture associée
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            
            // Métadonnées (masquer infos sensibles)
            'metadata' => $this->when(
                !empty($this->metadata),
                fn() => array_diff_key($this->metadata ?? [], ['card_number' => null, 'cvv' => null])
            ),
        ];
    }
}
