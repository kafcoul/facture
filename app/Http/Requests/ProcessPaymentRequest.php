<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request: Validation pour traitement de paiement
 */
class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'gateway' => ['required', 'string', 'in:stripe,paypal,wave,orange_money,mtn_momo'],
            'currency' => ['required', 'string', 'in:XOF,USD,EUR'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'return_url' => ['required', 'url'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'invoice_id.required' => 'L\'ID de la facture est requis',
            'invoice_id.exists' => 'La facture n\'existe pas',
            'amount.required' => 'Le montant est requis',
            'amount.min' => 'Le montant doit être supérieur à 0',
            'gateway.required' => 'La méthode de paiement est requise',
            'gateway.in' => 'Méthode de paiement non supportée',
            'return_url.required' => 'L\'URL de retour est requise',
            'return_url.url' => 'L\'URL de retour doit être valide',
        ];
    }
}
