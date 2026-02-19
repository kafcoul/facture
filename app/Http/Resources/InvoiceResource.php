<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource: Invoice
 * 
 * Serialization optimisée pour les réponses API
 */
class InvoiceResource extends JsonResource
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
            'number' => $this->number,
            'type' => $this->type,
            'status' => $this->status,
            
            // Montants
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'total' => $this->total,
            'currency' => $this->currency,
            
            // Dates
            'issued_at' => $this->issued_at?->toIso8601String(),
            'due_date' => $this->due_date?->toIso8601String(),
            'paid_at' => $this->paid_at?->toIso8601String(),
            
            // Relations (chargées conditionnellement)
            'client' => new ClientResource($this->whenLoaded('client')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            
            // Métadonnées
            'notes' => $this->notes,
            'terms' => $this->terms,
            'metadata' => $this->metadata,
            
            // Statut de paiement
            'is_paid' => $this->isPaid(),
            'is_overdue' => $this->isOverdue(),
            'days_until_due' => $this->due_date ? now()->diffInDays($this->due_date, false) : null,
            
            // PDF
            'pdf_url' => $this->pdf_path ? route('api.invoices.download-pdf', $this->id) : null,
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
