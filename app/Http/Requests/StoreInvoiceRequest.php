<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // L'utilisateur est déjà authentifié via le middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
            
            // Items (lignes de facture)
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'issue_date.required' => 'La date d\'émission est obligatoire.',
            'due_date.required' => 'La date d\'échéance est obligatoire.',
            'due_date.after_or_equal' => 'La date d\'échéance doit être égale ou postérieure à la date d\'émission.',
            'items.required' => 'Vous devez ajouter au moins une ligne à la facture.',
            'items.min' => 'Vous devez ajouter au moins une ligne à la facture.',
            'items.*.description.required' => 'La description est obligatoire pour chaque ligne.',
            'items.*.quantity.required' => 'La quantité est obligatoire pour chaque ligne.',
            'items.*.quantity.min' => 'La quantité doit être supérieure à 0.',
            'items.*.unit_price.required' => 'Le prix unitaire est obligatoire pour chaque ligne.',
            'items.*.unit_price.min' => 'Le prix unitaire doit être supérieur ou égal à 0.',
            'items.*.tax_rate.required' => 'Le taux de TVA est obligatoire pour chaque ligne.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'client_id' => 'client',
            'issue_date' => 'date d\'émission',
            'due_date' => 'date d\'échéance',
            'discount_percentage' => 'remise en pourcentage',
            'discount_amount' => 'montant de la remise',
            'items.*.description' => 'description',
            'items.*.quantity' => 'quantité',
            'items.*.unit_price' => 'prix unitaire',
            'items.*.tax_rate' => 'taux de TVA',
        ];
    }
}
