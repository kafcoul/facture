@extends('layouts.dashboard')

@section('title', 'Documentation API')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Documentation API</h1>
            <p class="text-gray-600 mt-1">Guide complet pour intégrer l'API Invoice SaaS</p>
        </div>
        <a href="{{ route('client.api-keys.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux clés API
        </a>
    </div>

    <div class="grid lg:grid-cols-4 gap-6">
        <!-- Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sticky top-4">
                <h3 class="font-semibold text-gray-900 mb-3">Navigation</h3>
                <nav class="space-y-1">
                    <a href="#auth" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Authentification</a>
                    <a href="#invoices" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Factures</a>
                    <a href="#clients" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Clients</a>
                    <a href="#products" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Produits</a>
                    <a href="#payments" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Paiements</a>
                    <a href="#errors" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Erreurs</a>
                    <a href="#rate-limits" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Limites</a>
                </nav>
            </div>
        </div>

        <!-- Contenu -->
        <div class="lg:col-span-3 space-y-8">
            <!-- URL de base -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">URL de base</h2>
                <code class="block bg-gray-800 text-green-400 rounded-lg p-4 text-sm">
                    {{ url('/api/v1') }}
                </code>
            </div>

            <!-- Authentification -->
            <div id="auth" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Authentification</h2>
                <p class="text-gray-600 mb-4">
                    Toutes les requêtes API doivent inclure votre clé API dans l'en-tête <code class="bg-gray-100 px-1 rounded">Authorization</code>.
                </p>
                <pre class="bg-gray-800 text-green-400 rounded-lg p-4 text-sm overflow-x-auto">
curl -X GET "{{ url('/api/v1/invoices') }}" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"</pre>
            </div>

            <!-- Factures -->
            <div id="invoices" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Factures</h2>
                
                <!-- Liste des factures -->
                <div class="mb-6">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-mono text-sm">GET</span>
                        <span class="font-medium">/invoices</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Récupérer la liste des factures avec pagination.</p>
                    <h4 class="font-medium text-gray-800 text-sm mb-2">Paramètres de requête</h4>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Paramètre</th>
                                <th class="px-3 py-2 text-left">Type</th>
                                <th class="px-3 py-2 text-left">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-3 py-2"><code>page</code></td>
                                <td class="px-3 py-2">integer</td>
                                <td class="px-3 py-2">Numéro de page (défaut: 1)</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2"><code>per_page</code></td>
                                <td class="px-3 py-2">integer</td>
                                <td class="px-3 py-2">Éléments par page (max: 100)</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2"><code>status</code></td>
                                <td class="px-3 py-2">string</td>
                                <td class="px-3 py-2">Filtrer par statut (draft, sent, paid, overdue)</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2"><code>client_id</code></td>
                                <td class="px-3 py-2">integer</td>
                                <td class="px-3 py-2">Filtrer par client</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Créer une facture -->
                <div class="mb-6 pt-6 border-t">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-mono text-sm">POST</span>
                        <span class="font-medium">/invoices</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Créer une nouvelle facture.</p>
                    <pre class="bg-gray-800 text-green-400 rounded-lg p-4 text-sm overflow-x-auto">
{
  "client_id": 1,
  "due_date": "2025-02-28",
  "notes": "Merci pour votre confiance",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "unit_price": 25000
    },
    {
      "description": "Service personnalisé",
      "quantity": 1,
      "unit_price": 50000
    }
  ]
}</pre>
                </div>

                <!-- Afficher une facture -->
                <div class="mb-6 pt-6 border-t">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-mono text-sm">GET</span>
                        <span class="font-medium">/invoices/{id}</span>
                    </div>
                    <p class="text-gray-600 text-sm">Récupérer les détails d'une facture spécifique.</p>
                </div>

                <!-- Envoyer une facture -->
                <div class="pt-6 border-t">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded font-mono text-sm">POST</span>
                        <span class="font-medium">/invoices/{id}/send</span>
                    </div>
                    <p class="text-gray-600 text-sm">Envoyer la facture par email au client.</p>
                </div>
            </div>

            <!-- Clients -->
            <div id="clients" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Clients</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-mono text-sm">GET</span>
                        <span class="font-medium">/clients</span>
                        <span class="text-gray-500 text-sm">- Liste des clients</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-mono text-sm">POST</span>
                        <span class="font-medium">/clients</span>
                        <span class="text-gray-500 text-sm">- Créer un client</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-mono text-sm">GET</span>
                        <span class="font-medium">/clients/{id}</span>
                        <span class="text-gray-500 text-sm">- Détails d'un client</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded font-mono text-sm">PUT</span>
                        <span class="font-medium">/clients/{id}</span>
                        <span class="text-gray-500 text-sm">- Modifier un client</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded font-mono text-sm">DELETE</span>
                        <span class="font-medium">/clients/{id}</span>
                        <span class="text-gray-500 text-sm">- Supprimer un client</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-medium text-gray-800 text-sm mb-2">Exemple de création</h4>
                    <pre class="bg-gray-800 text-green-400 rounded-lg p-4 text-sm overflow-x-auto">
{
  "name": "Société ABC",
  "email": "contact@abc.sn",
  "phone": "+221 77 123 45 67",
  "address": "123 Avenue Bourguiba, Dakar",
  "tax_number": "SN123456789"
}</pre>
                </div>
            </div>

            <!-- Produits -->
            <div id="products" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Produits</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-mono text-sm">GET</span>
                        <span class="font-medium">/products</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-mono text-sm">POST</span>
                        <span class="font-medium">/products</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-mono text-sm">GET</span>
                        <span class="font-medium">/products/{id}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded font-mono text-sm">PUT</span>
                        <span class="font-medium">/products/{id}</span>
                    </div>
                </div>
            </div>

            <!-- Paiements -->
            <div id="payments" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Paiements</h2>
                
                <div class="mb-6">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-mono text-sm">POST</span>
                        <span class="font-medium">/payments</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Enregistrer un paiement pour une facture.</p>
                    <pre class="bg-gray-800 text-green-400 rounded-lg p-4 text-sm overflow-x-auto">
{
  "invoice_id": 1,
  "amount": 75000,
  "payment_method": "bank_transfer",
  "reference": "VIR-2025-001",
  "payment_date": "2025-01-15"
}</pre>
                </div>

                <div class="pt-4 border-t">
                    <h4 class="font-medium text-gray-800 text-sm mb-2">Méthodes de paiement acceptées</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">cash</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">bank_transfer</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">mobile_money</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">wave</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">orange_money</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">card</span>
                    </div>
                </div>
            </div>

            <!-- Erreurs -->
            <div id="errors" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Codes d'erreur</h2>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Code</th>
                            <th class="px-3 py-2 text-left">Signification</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-3 py-2"><code class="text-red-600">400</code></td>
                            <td class="px-3 py-2">Requête invalide - Vérifiez les paramètres</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2"><code class="text-red-600">401</code></td>
                            <td class="px-3 py-2">Non authentifié - Clé API manquante ou invalide</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2"><code class="text-red-600">403</code></td>
                            <td class="px-3 py-2">Accès refusé - Permissions insuffisantes</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2"><code class="text-red-600">404</code></td>
                            <td class="px-3 py-2">Ressource non trouvée</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2"><code class="text-red-600">422</code></td>
                            <td class="px-3 py-2">Erreur de validation</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2"><code class="text-red-600">429</code></td>
                            <td class="px-3 py-2">Trop de requêtes - Limite atteinte</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2"><code class="text-red-600">500</code></td>
                            <td class="px-3 py-2">Erreur serveur</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-medium text-gray-800 text-sm mb-2">Format de réponse d'erreur</h4>
                    <pre class="bg-gray-800 text-green-400 rounded-lg p-4 text-sm overflow-x-auto">
{
  "error": {
    "code": "validation_error",
    "message": "Les données fournies sont invalides",
    "details": {
      "email": ["Le champ email est requis"]
    }
  }
}</pre>
                </div>
            </div>

            <!-- Limites -->
            <div id="rate-limits" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Limites de requêtes</h2>
                <p class="text-gray-600 mb-4">
                    Les requêtes API sont limitées pour garantir la stabilité du service.
                </p>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Plan</th>
                            <th class="px-3 py-2 text-left">Limite</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-3 py-2">Enterprise</td>
                            <td class="px-3 py-2">1000 requêtes/minute</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-medium text-gray-800 text-sm mb-2">En-têtes de réponse</h4>
                    <div class="space-y-2 text-sm">
                        <div><code class="bg-gray-100 px-1 rounded">X-RateLimit-Limit</code>: Limite totale</div>
                        <div><code class="bg-gray-100 px-1 rounded">X-RateLimit-Remaining</code>: Requêtes restantes</div>
                        <div><code class="bg-gray-100 px-1 rounded">X-RateLimit-Reset</code>: Timestamp de réinitialisation</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
