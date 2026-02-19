@extends('layouts.dashboard')

@section('title', 'Clés API')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Clés API</h1>
            <p class="text-gray-600 mt-1">Gérez vos clés d'accès à l'API Invoice SaaS</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('client.api-keys.documentation') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Documentation
            </a>
            <button onclick="openCreateModal()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouvelle clé API
            </button>
        </div>
    </div>

    <!-- Messages flash -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    <!-- Nouvelle clé générée -->
    @if(session('new_api_key'))
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Votre nouvelle clé API</h3>
                <p class="text-blue-700 text-sm mb-3">
                    Copiez cette clé maintenant. Pour des raisons de sécurité, elle ne sera plus affichée.
                </p>
                <div class="flex items-center space-x-2">
                    <code id="newApiKey" class="flex-1 bg-white border border-blue-300 rounded-lg px-4 py-2 font-mono text-sm break-all">
                        {{ session('new_api_key') }}
                    </code>
                    <button onclick="copyToClipboard('{{ session('new_api_key') }}')" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Liste des clés API -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Vos clés API ({{ $apiKeys->count() }}/10)</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($apiKeys as $apiKey)
            <div class="p-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 {{ $apiKey->is_active ? 'bg-green-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $apiKey->is_active ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $apiKey->name }}</p>
                            <div class="flex items-center space-x-3 text-sm text-gray-500">
                                <code class="bg-gray-100 px-2 py-0.5 rounded">{{ $apiKey->masked_key }}</code>
                                <span>•</span>
                                <span>Créée le {{ $apiKey->created_at->format('d/m/Y') }}</span>
                                @if($apiKey->last_used_at)
                                <span>•</span>
                                <span>Dernière utilisation: {{ $apiKey->last_used_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Statut -->
                        @if($apiKey->is_active)
                            @if($apiKey->expires_at && $apiKey->expires_at->isPast())
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Expirée</span>
                            @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>
                            @endif
                        @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Révoquée</span>
                        @endif

                        <!-- Utilisation -->
                        <span class="text-sm text-gray-500">{{ number_format($apiKey->usage_count) }} appels</span>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            @if($apiKey->is_active)
                            <form action="{{ route('client.api-keys.revoke', $apiKey->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        onclick="return confirm('Révoquer cette clé API ?')"
                                        class="text-yellow-600 hover:text-yellow-800 font-medium text-sm">
                                    Révoquer
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('client.api-keys.destroy', $apiKey->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Supprimer définitivement cette clé API ?')"
                                        class="text-red-600 hover:text-red-800 font-medium text-sm">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                @if($apiKey->permissions)
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($apiKey->permissions as $permission)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $permission }}</span>
                    @endforeach
                </div>
                @endif

                <!-- Expiration -->
                @if($apiKey->expires_at)
                <div class="mt-2 text-sm text-gray-500">
                    Expire le {{ $apiKey->expires_at->format('d/m/Y à H:i') }}
                </div>
                @endif
            </div>
            @empty
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <p class="text-gray-500 mb-4">Aucune clé API créée</p>
                <button onclick="openCreateModal()" class="text-blue-600 hover:text-blue-800 font-medium">
                    Créer votre première clé API
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Guide rapide -->
    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Utilisation de l'API</h3>
        <div class="space-y-4">
            <div>
                <h4 class="font-medium text-gray-800 mb-2">Authentification</h4>
                <code class="block bg-gray-800 text-green-400 rounded-lg p-4 text-sm overflow-x-auto">
curl -X GET "{{ url('/api/v1/invoices') }}" \
  -H "Authorization: Bearer VOTRE_CLE_API" \
  -H "Accept: application/json"
                </code>
            </div>
            <div>
                <h4 class="font-medium text-gray-800 mb-2">Endpoints disponibles</h4>
                <div class="grid md:grid-cols-2 gap-3">
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-mono">GET</span>
                        <span>/api/v1/invoices</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-mono">POST</span>
                        <span>/api/v1/invoices</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-mono">GET</span>
                        <span>/api/v1/clients</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded font-mono">POST</span>
                        <span>/api/v1/payments</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Création -->
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeCreateModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Nouvelle clé API</h3>
            <form action="{{ route('client.api-keys.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la clé *</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: Production, Test, Mobile App...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiration (jours)</label>
                        <select name="expires_in"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Jamais</option>
                            <option value="30">30 jours</option>
                            <option value="90">90 jours</option>
                            <option value="180">180 jours</option>
                            <option value="365">1 an</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="*" checked
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Toutes les permissions</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="invoices.*"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Factures uniquement</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="clients.*"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Clients uniquement</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="payments.*"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Paiements uniquement</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCreateModal()" 
                            class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                        Générer la clé
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Clé API copiée !');
    });
}
</script>
@endsection
