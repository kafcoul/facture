<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    @if($invoice->status !== 'paid')
        @php
            $gateway = request()->get('gateway', config('payments.default', 'stripe'));
        @endphp
        
        @if($gateway === 'stripe')
        <script src="https://js.stripe.com/v3/"></script>
        @elseif($gateway === 'paystack')
        <script src="https://js.paystack.co/v1/inline.js"></script>
        @elseif($gateway === 'flutterwave')
        <script src="https://checkout.flutterwave.com/v3.js"></script>
        @elseif($gateway === 'kkiapay')
        <script src="https://cdn.kkiapay.me/k.js"></script>
        @elseif($gateway === 'fedapay')
        <script src="https://checkout.fedapay.com/v1.js"></script>
        @elseif($gateway === 'cinetpay')
        <script src="https://cdn.cinetpay.com/seamless/main.js"></script>
        @endif
    @endif
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <!-- Status Banner -->
            @if($invoice->status === 'paid')
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            ✅ Cette facture a été payée le {{ $invoice->payments()->latest()->first()?->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
            @elseif($invoice->status === 'overdue')
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            ⚠️ Cette facture est en retard de paiement (échéance: {{ $invoice->due_date?->format('d/m/Y') }})
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Invoice Header -->
            <div class="bg-white shadow-sm rounded-lg mb-6 p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ env('COMPANY_ADDRESS', 'Adresse de votre entreprise') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">FACTURE</div>
                        <div class="text-2xl font-bold text-gray-900">#{{ $invoice->invoice_number }}</div>
                        <div class="mt-2">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $invoice->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Facturé à:</h3>
                        <div class="text-gray-900 font-medium">{{ $invoice->client->name }}</div>
                        <div class="text-sm text-gray-600">{{ $invoice->client->email }}</div>
                        @if($invoice->client->phone)
                        <div class="text-sm text-gray-600">{{ $invoice->client->phone }}</div>
                        @endif
                        @if($invoice->client->address)
                        <div class="text-sm text-gray-600 mt-1">{{ $invoice->client->address }}</div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="mb-2">
                            <span class="text-sm text-gray-600">Date d'émission:</span>
                            <span class="font-medium">{{ $invoice->issue_date->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Date d'échéance:</span>
                            <span class="font-medium">{{ $invoice->due_date?->format('d/m/Y') ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="bg-white shadow-sm rounded-lg mb-6 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->description }}</div>
                                @if($item->product)
                                <div class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">{{ number_format($item->unit_price, 2) }} {{ $invoice->currency ?? 'EUR' }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ number_format($item->total, 2) }} {{ $invoice->currency ?? 'EUR' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="bg-white shadow-sm rounded-lg mb-6 p-6">
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2">
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">Sous-total HT:</span>
                            <span class="font-medium">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency ?? 'EUR' }}</span>
                        </div>
                        @if($invoice->discount > 0)
                        <div class="flex justify-between py-2 border-b border-gray-200 text-green-600">
                            <span>Remise:</span>
                            <span>-{{ number_format($invoice->discount, 2) }} {{ $invoice->currency ?? 'EUR' }}</span>
                        </div>
                        @endif
                        @if($invoice->tax > 0)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">TVA:</span>
                            <span class="font-medium">{{ number_format($invoice->tax, 2) }} {{ $invoice->currency ?? 'EUR' }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between py-3 mt-2">
                            <span class="text-lg font-bold text-gray-900">Total TTC:</span>
                            <span class="text-lg font-bold text-blue-600">{{ number_format($invoice->total_ttc, 2) }} {{ $invoice->currency ?? 'EUR' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
            <div class="bg-white shadow-sm rounded-lg mb-6 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">💳 Choisissez votre moyen de paiement</h2>
                
                @php
                    $availableGateways = \App\Services\PaymentGatewayService::getAvailableGateways();
                    $selectedGateway = request()->get('gateway', config('payments.default', 'stripe'));
                @endphp

                @if(count($availableGateways) > 1)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                    @foreach($availableGateways as $key => $gateway)
                    <button 
                        onclick="selectGateway('{{ $key }}')"
                        class="gateway-btn p-4 border-2 rounded-lg text-center transition-all hover:border-blue-500 hover:shadow-md
                               {{ $selectedGateway === $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}"
                        data-gateway="{{ $key }}">
                        <div class="text-2xl mb-2">
                            @if($key === 'stripe') 💳
                            @elseif($key === 'paystack') 🇳🇬
                            @elseif($key === 'flutterwave') 🦋
                            @elseif($key === 'wave') 🌊
                            @elseif($key === 'orange_money') 🍊
                            @elseif($key === 'mtn_momo') 📱
                            @elseif($key === 'mpesa') 💚
                            @elseif($key === 'fedapay') 💰
                            @elseif($key === 'kkiapay') 🔐
                            @elseif($key === 'cinetpay') 🎬
                            @else 💳
                            @endif
                        </div>
                        <div class="font-medium text-sm">{{ $gateway['name'] }}</div>
                    </button>
                    @endforeach
                </div>
                @endif

                <div id="payment-form-container">
                    <!-- Payment form will be injected here -->
                </div>

                <div id="payment-messages" class="mt-4"></div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex justify-center gap-4">
                <a href="{{ route('invoices.pdf', ['invoice' => $invoice]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger PDF
                </a>
            </div>

            @if($invoice->notes)
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">📝 Notes:</h3>
                <p class="text-sm text-blue-800">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
    <script>
        const selectedGateway = '{{ $selectedGateway }}';
        const invoiceId = '{{ $invoice->id }}';
        const invoiceTotal = {{ $invoice->total_ttc }};
        const currency = '{{ strtolower($invoice->currency ?? "eur") }}';
        const clientEmail = '{{ $invoice->client->email }}';
        const clientName = '{{ $invoice->client->name }}';

        // Gateway selection
        function selectGateway(gateway) {
            window.location.href = `?gateway=${gateway}`;
        }

        // Initialize payment based on selected gateway
        document.addEventListener('DOMContentLoaded', function() {
            initializePayment(selectedGateway);
        });

        function initializePayment(gateway) {
            const container = document.getElementById('payment-form-container');
            
            switch(gateway) {
                case 'stripe':
                    initStripe(container);
                    break;
                case 'paystack':
                    initPaystack(container);
                    break;
                case 'flutterwave':
                    initFlutterwave(container);
                    break;
                case 'wave':
                    initWave(container);
                    break;
                case 'fedapay':
                    initFedapay(container);
                    break;
                case 'kkiapay':
                    initKkiapay(container);
                    break;
                case 'cinetpay':
                    initCinetpay(container);
                    break;
                default:
                    container.innerHTML = '<p class="text-gray-600">Ce moyen de paiement sera bientôt disponible.</p>';
            }
        }

        // ========== STRIPE ==========
        function initStripe(container) {
            container.innerHTML = `
                <form id="stripe-payment-form">
                    <div id="stripe-payment-element" class="mb-4"></div>
                    <button type="submit" id="stripe-submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        Payer ${invoiceTotal.toFixed(2)} ${currency.toUpperCase()}
                    </button>
                </form>
            `;

            const stripe = Stripe('{{ config("payments.gateways.stripe.key") }}');
            
            fetch(`/invoices/${invoiceId}/payment/initialize`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ gateway: 'stripe' })
            })
            .then(r => r.json())
            .then(data => {
                const elements = stripe.elements({ clientSecret: data.client_secret });
                const paymentElement = elements.create('payment');
                paymentElement.mount('#stripe-payment-element');

                document.getElementById('stripe-payment-form').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const {error} = await stripe.confirmPayment({
                        elements,
                        confirmParams: {
                            return_url: window.location.href,
                        },
                    });
                    if (error) showMessage(error.message, 'error');
                });
            });
        }

        // ========== PAYSTACK ==========
        function initPaystack(container) {
            container.innerHTML = `
                <button id="paystack-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Payer avec Paystack - ${invoiceTotal.toFixed(2)} ${currency.toUpperCase()}
                </button>
            `;

            document.getElementById('paystack-button').addEventListener('click', function() {
                fetch(`/invoices/${invoiceId}/payment/initialize`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ gateway: 'paystack' })
                })
                .then(r => r.json())
                .then(data => {
                    const handler = PaystackPop.setup({
                        key: '{{ config("payments.gateways.paystack.public_key") }}',
                        email: clientEmail,
                        amount: invoiceTotal * 100,
                        currency: currency.toUpperCase(),
                        ref: data.reference,
                        callback: function(response) {
                            showMessage('Paiement réussi!', 'success');
                            window.location.reload();
                        },
                        onClose: function() {
                            showMessage('Paiement annulé', 'warning');
                        }
                    });
                    handler.openIframe();
                });
            });
        }

        // ========== FLUTTERWAVE ==========
        function initFlutterwave(container) {
            container.innerHTML = `
                <button id="flw-button" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Payer avec Flutterwave - ${invoiceTotal.toFixed(2)} ${currency.toUpperCase()}
                </button>
            `;

            document.getElementById('flw-button').addEventListener('click', function() {
                fetch(`/invoices/${invoiceId}/payment/initialize`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ gateway: 'flutterwave' })
                })
                .then(r => r.json())
                .then(data => {
                    FlutterwaveCheckout({
                        public_key: '{{ config("payments.gateways.flutterwave.public_key") }}',
                        tx_ref: data.reference,
                        amount: invoiceTotal,
                        currency: currency.toUpperCase(),
                        payment_options: "card,mobilemoney,ussd",
                        customer: {
                            email: clientEmail,
                            name: clientName,
                        },
                        callback: function (data) {
                            showMessage('Paiement réussi!', 'success');
                            window.location.reload();
                        },
                        onclose: function() {
                            showMessage('Paiement annulé', 'warning');
                        }
                    });
                });
            });
        }

        // ========== WAVE ==========
        function initWave(container) {
            container.innerHTML = `
                <button id="wave-button" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Payer avec Wave - ${invoiceTotal.toFixed(2)} XOF
                </button>
            `;

            document.getElementById('wave-button').addEventListener('click', function() {
                fetch(`/invoices/${invoiceId}/payment/initialize`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ gateway: 'wave' })
                })
                .then(r => r.json())
                .then(data => {
                    window.location.href = data.wave_launch_url;
                });
            });
        }

        // ========== FEDAPAY ==========
        function initFedapay(container) {
            container.innerHTML = `
                <button id="fedapay-button" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Payer avec FedaPay - ${invoiceTotal.toFixed(2)} ${currency.toUpperCase()}
                </button>
            `;

            document.getElementById('fedapay-button').addEventListener('click', function() {
                fetch(`/invoices/${invoiceId}/payment/initialize`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ gateway: 'fedapay' })
                })
                .then(r => r.json())
                .then(data => {
                    FedaPay.init({
                        public_key: '{{ config("payments.gateways.fedapay.public_key") }}',
                        transaction: {
                            amount: invoiceTotal,
                            description: `Facture #{{ $invoice->invoice_number }}`
                        },
                        customer: {
                            email: clientEmail,
                            firstname: clientName
                        },
                        onComplete: function(resp) {
                            if(resp.reason === 'CHECKOUT_COMPLETED') {
                                showMessage('Paiement réussi!', 'success');
                                window.location.reload();
                            }
                        }
                    });
                    FedaPay.open();
                });
            });
        }

        // ========== KKIAPAY ==========
        function initKkiapay(container) {
            container.innerHTML = `
                <button id="kkiapay-button" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Payer avec Kkiapay - ${invoiceTotal.toFixed(2)} ${currency.toUpperCase()}
                </button>
            `;

            document.getElementById('kkiapay-button').addEventListener('click', function() {
                openKkiapayWidget({
                    amount: invoiceTotal,
                    api_key: '{{ config("payments.gateways.kkiapay.public_key") }}',
                    sandbox: true,
                    email: clientEmail,
                    phone: '',
                    name: clientName,
                    reason: `Facture #{{ $invoice->invoice_number }}`
                });

                addSuccessListener(function(response) {
                    showMessage('Paiement réussi!', 'success');
                    window.location.reload();
                });
            });
        }

        // ========== CINETPAY ==========
        function initCinetpay(container) {
            container.innerHTML = `
                <button id="cinetpay-button" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Payer avec CinetPay - ${invoiceTotal.toFixed(2)} ${currency.toUpperCase()}
                </button>
            `;

            document.getElementById('cinetpay-button').addEventListener('click', function() {
                fetch(`/invoices/${invoiceId}/payment/initialize`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ gateway: 'cinetpay' })
                })
                .then(r => r.json())
                .then(data => {
                    CinetPay.setConfig({
                        apikey: '{{ config("payments.gateways.cinetpay.api_key") }}',
                        site_id: '{{ config("payments.gateways.cinetpay.site_id") }}',
                        notify_url: '{{ route("webhooks.cinetpay") }}'
                    });

                    CinetPay.seamless({
                        transaction_id: data.reference,
                        amount: invoiceTotal,
                        currency: currency.toUpperCase(),
                        customer_name: clientName,
                        customer_email: clientEmail,
                        description: `Facture #{{ $invoice->invoice_number }}`
                    });

                    CinetPay.on('success', function() {
                        showMessage('Paiement réussi!', 'success');
                        window.location.reload();
                    });
                });
            });
        }

        // Helper function to show messages
        function showMessage(message, type = 'info') {
            const messagesDiv = document.getElementById('payment-messages');
            const colors = {
                success: 'bg-green-100 border-green-400 text-green-700',
                error: 'bg-red-100 border-red-400 text-red-700',
                warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
                info: 'bg-blue-100 border-blue-400 text-blue-700'
            };

            messagesDiv.innerHTML = `
                <div class="border-l-4 p-4 ${colors[type]} rounded">
                    <p class="font-medium">${message}</p>
                </div>
            `;
        }
    </script>
    @endif
</body>
</html>
