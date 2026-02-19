<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header with Company Logo -->
            <div class="bg-white shadow-sm rounded-lg mb-6 p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ env('COMPANY_ADDRESS', 'Adresse de votre entreprise') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">FACTURE</div>
                        <div class="text-2xl font-bold text-gray-900">#{{ $invoice->invoice_number }}</div>
                    </div>
                </div>
            </div>

            <!-- Status Alert -->
            <div class="mb-6">
                @if($invoice->status === 'paid')
                    <div class="bg-green-50 border-l-4 border-green-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700 font-medium">Cette facture a été payée</p>
                            </div>
                        </div>
                    </div>
                @elseif($invoice->status === 'overdue')
                    <div class="bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-medium">Cette facture est en retard de paiement</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700 font-medium">En attente de paiement</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Invoice Details -->
            <div class="bg-white shadow-sm rounded-lg mb-6 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Client Info -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Facturé à</h3>
                        <div class="text-gray-900">
                            <p class="font-semibold text-lg">{{ $invoice->client->name }}</p>
                            <p class="text-gray-600">{{ $invoice->client->email }}</p>
                            @if($invoice->client->phone)
                                <p class="text-gray-600">{{ $invoice->client->phone }}</p>
                            @endif
                            @if($invoice->client->address)
                                <p class="text-gray-600 mt-2">{{ $invoice->client->address }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Invoice Info -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Détails de la facture</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Date d'émission:</dt>
                                <dd class="font-medium text-gray-900">{{ $invoice->issue_date->format('d/m/Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Date d'échéance:</dt>
                                <dd class="font-medium text-gray-900">{{ $invoice->due_date->format('d/m/Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Statut:</dt>
                                <dd>
                                    @if($invoice->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Payé
                                        </span>
                                    @elseif($invoice->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            En attente
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            En retard
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white shadow-sm rounded-lg mb-6 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Qté
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prix unit.
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Taxe
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->description }}</div>
                                    @if($item->product)
                                        <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    {{ number_format($item->unit_price, 2) }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    {{ number_format($item->tax_rate, 1) }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                    {{ number_format($item->total, 2) }} €
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals -->
            <div class="bg-white shadow-sm rounded-lg mb-6 p-6">
                <div class="flex justify-end">
                    <div class="w-full max-w-sm space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Sous-total:</span>
                            <span class="font-medium text-gray-900">{{ number_format($invoice->subtotal, 2) }} €</span>
                        </div>
                        
                        @if($invoice->tax_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">TVA:</span>
                            <span class="font-medium text-gray-900">{{ number_format($invoice->tax_amount, 2) }} €</span>
                        </div>
                        @endif
                        
                        @if($invoice->discount_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Remise:</span>
                            <span class="font-medium text-gray-900">-{{ number_format($invoice->discount_amount, 2) }} €</span>
                        </div>
                        @endif
                        
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-base font-semibold text-gray-900">TOTAL:</span>
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($invoice->total, 2) }} €</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <h4 class="text-sm font-semibold text-blue-900 mb-2">Notes</h4>
                <p class="text-sm text-blue-800">{{ $invoice->notes }}</p>
            </div>
            @endif

            <!-- Payment Section -->
            @if($invoice->status !== 'paid')
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payer cette facture</h3>
                <div id="payment-element" class="mb-4">
                    <!-- Stripe Payment Element will be inserted here -->
                </div>
                <button id="submit-payment" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Payer {{ number_format($invoice->total, 2) }} €
                </button>
                <div id="payment-message" class="mt-4 text-sm"></div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex justify-center space-x-4">
                <a href="{{ route('invoices.download', $invoice->uuid) }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Télécharger PDF
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>Merci pour votre confiance !</p>
                <p class="mt-1">{{ config('app.name') }} - {{ env('COMPANY_EMAIL', 'contact@example.com') }}</p>
            </div>
        </div>
    </div>

    @if($invoice->status !== 'paid')
    <script>
        // Stripe initialization
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        
        // Create payment intent and initialize elements
        async function initializePayment() {
            try {
                const response = await fetch('{{ route('invoices.create-payment-intent', $invoice->uuid) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const { clientSecret } = await response.json();
                
                const elements = stripe.elements({ clientSecret });
                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');
                
                // Handle form submission
                const submitButton = document.getElementById('submit-payment');
                submitButton.addEventListener('click', async (e) => {
                    e.preventDefault();
                    submitButton.disabled = true;
                    submitButton.textContent = 'Traitement...';
                    
                    const { error } = await stripe.confirmPayment({
                        elements,
                        confirmParams: {
                            return_url: '{{ route('invoices.payment-success', $invoice->uuid) }}'
                        }
                    });
                    
                    if (error) {
                        const messageDiv = document.getElementById('payment-message');
                        messageDiv.textContent = error.message;
                        messageDiv.className = 'mt-4 text-sm text-red-600';
                        submitButton.disabled = false;
                        submitButton.textContent = 'Payer {{ number_format($invoice->total, 2) }} €';
                    }
                });
            } catch (error) {
                console.error('Error initializing payment:', error);
                const messageDiv = document.getElementById('payment-message');
                messageDiv.textContent = 'Erreur lors de l\'initialisation du paiement';
                messageDiv.className = 'mt-4 text-sm text-red-600';
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', initializePayment);
    </script>
    @endif
</body>
</html>
