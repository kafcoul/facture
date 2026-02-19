<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitializePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gateway' => ['required', 'string', 'in:stripe,paystack,flutterwave,wave,mpesa,fedapay,kkiapay,cinetpay'],
            'email' => ['nullable', 'email'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'gateway.required' => 'Please select a payment method',
            'gateway.in' => 'The selected payment method is not available',
            'email.email' => 'Please provide a valid email address',
        ];
    }
}
