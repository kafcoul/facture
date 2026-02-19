<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de Paiement - Facture {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-4">
                    <svg class="h-16 w-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                    Paiement Échoué
                </h2>
                <p class="text-gray-600 mb-8">
                    Le paiement n'a pas pu être traité
                </p>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-red-800 mb-2">
                        ⚠️ Erreur lors du paiement
                    </p>
                    <p class="text-sm text-red-700">
                        {{ $error ?? 'Une erreur est survenue lors du traitement de votre paiement. Veuillez réessayer.' }}
                    </p>
                </div>

                <div class="border-b pb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Facture:</span>
                        <span class="font-bold text-gray-900">#{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Montant à payer:</span>
                        <span class="font-bold text-gray-900 text-xl">
                            {{ number_format($invoice->total_ttc, 2) }} {{ $invoice->currency ?? 'EUR' }}
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <p class="text-sm text-gray-600 mb-3">
                        Raisons possibles de l'échec :
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2 ml-4">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Fonds insuffisants</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Informations de paiement incorrectes</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Problème de connexion réseau</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Limite de transaction dépassée</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col space-y-3">
                <a href="{{ route('invoices.public', $invoice->uuid) }}" 
                   class="w-full flex justify-center items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Réessayer le paiement
                </a>
                
                <a href="{{ route('invoices.pdf', $invoice->uuid) }}" 
                   class="w-full flex justify-center items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Télécharger la facture
                </a>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    💡 <strong>Besoin d'aide ?</strong><br>
                    Contactez-nous à {{ env('MAIL_FROM_ADDRESS', 'contact@example.com') }} ou par téléphone au {{ env('COMPANY_PHONE', '+000 00 00 00 00') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
