<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - Facture {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-4">
                    <svg class="h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                    Paiement Réussi ! 🎉
                </h2>
                <p class="text-gray-600 mb-8">
                    Votre paiement a été traité avec succès
                </p>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
                <div class="border-b pb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Facture:</span>
                        <span class="font-bold text-gray-900">#{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Montant payé:</span>
                        <span class="font-bold text-green-600 text-xl">
                            {{ number_format($invoice->total_ttc, 2) }} {{ $invoice->currency ?? 'EUR' }}
                        </span>
                    </div>
                    @if($invoice->payments->isNotEmpty())
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Date du paiement:</span>
                        <span class="font-medium text-gray-900">
                            {{ $invoice->payments->last()->payment_date->format('d/m/Y à H:i') }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="pt-2">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-green-800">
                            ✅ Un email de confirmation a été envoyé à <strong>{{ $invoice->client->email }}</strong>
                        </p>
                    </div>

                    <div class="text-sm text-gray-600">
                        <p>📧 Vous recevrez une copie de la facture par email.</p>
                        <p class="mt-2">💼 Merci pour votre confiance !</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col space-y-3">
                <a href="{{ route('invoices.pdf', $invoice->uuid) }}" 
                   class="w-full flex justify-center items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger la facture PDF
                </a>
                
                <a href="{{ route('invoices.public', $invoice->uuid) }}" 
                   class="w-full flex justify-center items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Voir la facture
                </a>
            </div>

            <p class="text-center text-xs text-gray-500 mt-6">
                Pour toute question, contactez-nous à {{ env('MAIL_FROM_ADDRESS', 'contact@example.com') }}
            </p>
        </div>
    </div>
</body>
</html>
