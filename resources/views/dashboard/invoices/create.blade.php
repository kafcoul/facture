@extends('layouts.dashboard')

@section('title', 'Créer une Facture')

@section('content')
    <div x-data="invoiceCreator()" x-init="init()" class="min-h-screen">
        <!-- En-tête avec animation -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Nouvelle Facture
                    </h1>
                    <p class="mt-1 text-gray-500">Créez une facture professionnelle en quelques clics</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span x-show="isSaving" class="flex items-center text-sm text-gray-500">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Sauvegarde auto...
                    </span>
                    <span x-show="lastSaved" x-cloak class="text-xs text-green-500 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Brouillon sauvegardé
                    </span>
                </div>
            </div>

            <!-- Barre de progression -->
            <div class="mt-6 flex items-center space-x-4">
                <div class="flex-1 flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full transition-all duration-300"
                        :class="step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500'">
                        <span class="text-sm font-medium">1</span>
                    </div>
                    <span class="ml-2 text-sm font-medium"
                        :class="step >= 1 ? 'text-indigo-600' : 'text-gray-500'">Client</span>
                </div>
                <div class="flex-1 h-1 rounded-full" :class="step >= 2 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex-1 flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full transition-all duration-300"
                        :class="step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500'">
                        <span class="text-sm font-medium">2</span>
                    </div>
                    <span class="ml-2 text-sm font-medium"
                        :class="step >= 2 ? 'text-indigo-600' : 'text-gray-500'">Articles</span>
                </div>
                <div class="flex-1 h-1 rounded-full" :class="step >= 3 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex-1 flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full transition-all duration-300"
                        :class="step >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500'">
                        <span class="text-sm font-medium">3</span>
                    </div>
                    <span class="ml-2 text-sm font-medium"
                        :class="step >= 3 ? 'text-indigo-600' : 'text-gray-500'">Finaliser</span>
                </div>
            </div>
        </div>

        <form action="{{ route('client.invoices.store') }}" method="POST" @submit="validateForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Section Client -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md"
                        :class="{ 'ring-2 ring-indigo-500': step === 1 }">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Informations Client</h2>
                                    <p class="text-sm text-gray-500">Sélectionnez ou créez un client</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <div class="relative">
                                    <input type="text" x-model="clientSearch"
                                        @focus="showClientDropdown = true; step = 1" @input="filterClients()"
                                        placeholder="Rechercher un client par nom ou email..."
                                        class="w-full pl-12 pr-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <div x-show="selectedClient" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                        <button type="button" @click="clearClient()"
                                            class="text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Dropdown clients -->
                                <div x-show="showClientDropdown && filteredClients.length > 0" x-cloak
                                    @click.away="showClientDropdown = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-gray-100 max-h-80 overflow-y-auto">
                                    <template x-for="client in filteredClients" :key="client.id">
                                        <button type="button" @click="selectClient(client)"
                                            class="w-full px-4 py-3 text-left hover:bg-indigo-50 flex items-center space-x-4 transition-colors border-b border-gray-50 last:border-0">
                                            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg"
                                                x-text="client.name.charAt(0).toUpperCase()">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 truncate" x-text="client.name"></p>
                                                <p class="text-sm text-gray-500 truncate" x-text="client.email"></p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700"
                                                    x-text="client.invoices_count + ' factures'"></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                <!-- Aucun résultat -->
                                <div x-show="showClientDropdown && clientSearch.length > 0 && filteredClients.length === 0"
                                    x-cloak
                                    class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-gray-100 p-6 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-gray-500">Aucun client trouvé</p>
                                    <button type="button" @click="openNewClientModal()"
                                        class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Créer ce client
                                    </button>
                                </div>
                            </div>

                            <!-- Client sélectionné -->
                            <div x-show="selectedClient" x-cloak class="mt-4">
                                <input type="hidden" name="client_id" :value="selectedClient?.id">
                                <div
                                    class="p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl"
                                            x-text="selectedClient?.name?.charAt(0).toUpperCase()">
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="font-semibold text-gray-900" x-text="selectedClient?.name"></h4>
                                            <p class="text-sm text-gray-600" x-text="selectedClient?.email"></p>
                                            <p class="text-sm text-gray-500" x-text="selectedClient?.phone"></p>
                                            <p class="text-sm text-gray-500 mt-1" x-text="selectedClient?.address"></p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Sélectionné
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton nouveau client -->
                            <div x-show="!selectedClient" class="mt-4 text-center">
                                <button type="button" @click="openNewClientModal()"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Nouveau client
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Section Articles -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md"
                        :class="{ 'ring-2 ring-indigo-500': step === 2 }">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-xl bg-green-600 text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h2 class="text-lg font-semibold text-gray-900">Articles & Services</h2>
                                        <p class="text-sm text-gray-500">Ajoutez les éléments de la facture</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                                        :class="items.some(i => i.product_id || i.unit_price > 0) ?
                                            'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                        x-text="items.filter(i => i.product_id || i.unit_price > 0).length + ' article(s)'"></span>
                                    <button type="button" @click="addItem(); step = 2"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6" @click="step = 2">
                            <!-- État vide amélioré -->
                            <div x-show="items.length === 1 && !items[0].product_id && items[0].unit_price === 0"
                                class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-800">Comment ajouter des articles ?</p>
                                        <ul class="mt-1 text-xs text-blue-600 space-y-1">
                                            <li>• Sélectionnez un produit dans la liste déroulante</li>
                                            <li>• Ou saisissez manuellement un prix et une description</li>
                                            <li>• Utilisez les boutons d'ajout rapide ci-dessous</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Suggestion rapide (déplacé en haut pour meilleure visibilité) -->
                            @if ($products->count() > 0)
                                <div class="mb-5 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">⚡ Ajout
                                        rapide</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($products->take(8) as $product)
                                            <button type="button"
                                                @click="quickAddProduct({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->unit_price }}, {{ $product->tax_rate ?? 18 }})"
                                                class="inline-flex items-center px-3 py-2 text-xs font-medium bg-white border border-gray-200 hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                                                <svg class="w-3 h-3 mr-1.5 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                {{ $product->name }}
                                                <span
                                                    class="ml-1.5 text-gray-400">{{ number_format($product->unit_price, 0, ',', ' ') }}
                                                    XOF</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Liste des articles -->
                            <div class="space-y-4">
                                <template x-for="(item, index) in items" :key="item.key">
                                    <div class="group relative p-5 rounded-xl border-2 transition-all duration-200"
                                        :class="item.product_id || item.unit_price > 0 ?
                                            'bg-white border-green-200 hover:border-green-300 shadow-sm' :
                                            'bg-gray-50 border-dashed border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/30'"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100">

                                        <!-- Numéro d'article -->
                                        <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full text-white flex items-center justify-center text-sm font-bold shadow-lg"
                                            :class="item.product_id || item.unit_price > 0 ? 'bg-green-600' : 'bg-indigo-600'"
                                            x-text="index + 1"></div>

                                        <!-- Bouton supprimer -->
                                        <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                            class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600 shadow-lg z-10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>

                                        <div class="grid grid-cols-12 gap-3 lg:gap-4">
                                            <!-- Produit/Description -->
                                            <div class="col-span-12 lg:col-span-5">
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                        </path>
                                                    </svg>
                                                    Produit / Service
                                                </label>
                                                <div class="relative">
                                                    <select :name="'items[' + index + '][product_id]'"
                                                        x-model="item.product_id" @change="updateItemFromProduct(index)"
                                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-900 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none cursor-pointer text-sm">
                                                        <option value="">-- Sélectionner un produit --</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                data-price="{{ $product->unit_price }}"
                                                                data-tax="{{ $product->tax_rate }}"
                                                                data-name="{{ $product->name }}">
                                                                {{ $product->name }} —
                                                                {{ number_format($product->unit_price, 0, ',', ' ') }} XOF
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <input type="text" :name="'items[' + index + '][description]'"
                                                    x-model="item.description"
                                                    placeholder="Description ou notes (auto-rempli avec le produit)"
                                                    class="mt-2 w-full px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-900 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>

                                            <!-- Quantité -->
                                            <div class="col-span-4 lg:col-span-2">
                                                <label
                                                    class="block text-xs font-semibold text-gray-600 mb-1.5">Quantité</label>
                                                <div class="flex items-center">
                                                    <button type="button" @click="decrementQty(index)"
                                                        class="p-2.5 bg-gray-100 rounded-l-xl hover:bg-gray-200 active:bg-gray-300 transition-colors border-y-2 border-l-2 border-gray-200">
                                                        <svg class="w-4 h-4 text-gray-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                    <input type="number" :name="'items[' + index + '][quantity]'"
                                                        x-model.number="item.quantity" @input="calculateTotals()"
                                                        min="1" step="1"
                                                        class="w-full px-2 py-2.5 border-y-2 border-gray-200 text-center text-gray-900 font-bold bg-white focus:ring-0 focus:border-indigo-500 text-sm">
                                                    <button type="button" @click="incrementQty(index)"
                                                        class="p-2.5 bg-gray-100 rounded-r-xl hover:bg-gray-200 active:bg-gray-300 transition-colors border-y-2 border-r-2 border-gray-200">
                                                        <svg class="w-4 h-4 text-gray-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Prix unitaire -->
                                            <div class="col-span-4 lg:col-span-2">
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Prix
                                                    unitaire</label>
                                                <div class="relative">
                                                    <input type="number" :name="'items[' + index + '][unit_price]'"
                                                        x-model.number="item.unit_price" @input="calculateTotals()"
                                                        placeholder="0" min="0" step="100"
                                                        class="w-full pl-4 pr-14 py-2.5 border-2 border-gray-200 rounded-xl text-gray-900 font-bold bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                                    <span
                                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-medium text-gray-400">XOF</span>
                                                </div>
                                            </div>

                                            <!-- TVA -->
                                            <div class="col-span-4 lg:col-span-1">
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">TVA</label>
                                                <div class="relative">
                                                    <input type="number" :name="'items[' + index + '][tax_rate]'"
                                                        x-model.number="item.tax_rate" @input="calculateTotals()"
                                                        min="0" max="100" placeholder="18"
                                                        class="w-full pl-4 pr-7 py-2.5 border-2 border-gray-200 rounded-xl text-gray-900 font-bold bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                                    <span
                                                        class="absolute inset-y-0 right-0 pr-2 flex items-center text-xs font-medium text-gray-400">%</span>
                                                </div>
                                            </div>

                                            <!-- Total ligne -->
                                            <div class="col-span-12 lg:col-span-2">
                                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total
                                                    HT</label>
                                                <div class="px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl text-white text-center font-bold text-sm"
                                                    x-text="formatCurrency(item.quantity * item.unit_price)">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Indicateur de ligne complète -->
                                        <div x-show="item.product_id && item.unit_price > 0"
                                            class="mt-3 flex items-center text-xs text-green-600">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Ligne complète
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Bouton ajouter article -->
                            <button type="button" @click="addItem()"
                                class="mt-6 w-full py-4 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 hover:border-green-500 hover:text-green-600 hover:bg-green-50 transition-all duration-200 flex items-center justify-center group">
                                <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="font-medium">Ajouter un nouvel article</span>
                            </button>
                        </div>
                    </div>

                    <!-- Section Options -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md"
                        :class="{ 'ring-2 ring-indigo-500': step === 3 }">
                        <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-600 text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Options & Personnalisation</h2>
                                    <p class="text-sm text-gray-500">Remises, notes et conditions</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6" @click="step = 3">
                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de facture</label>
                                    <input type="date" name="issue_date" x-model="invoiceDate"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date d'échéance</label>
                                    <input type="date" name="due_date" x-model="dueDate"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <!-- Remise -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Remise</label>
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1">
                                        <input type="number" name="discount_value" x-model.number="discountValue"
                                            @input="calculateTotals()" placeholder="0" min="0"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="flex bg-gray-100 rounded-xl p-1">
                                        <button type="button" @click="discountType = 'percentage'; calculateTotals()"
                                            :class="discountType === 'percentage' ? 'bg-white shadow text-indigo-600' :
                                                'text-gray-500'"
                                            class="px-4 py-2 rounded-lg font-medium transition-all duration-200">
                                            %
                                        </button>
                                        <button type="button" @click="discountType = 'fixed'; calculateTotals()"
                                            :class="discountType === 'fixed' ? 'bg-white shadow text-indigo-600' :
                                                'text-gray-500'"
                                            class="px-4 py-2 rounded-lg font-medium transition-all duration-200">
                                            XOF
                                        </button>
                                    </div>
                                    <input type="hidden" name="discount_type" :value="discountType">
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes pour le client</label>
                                <textarea name="notes" rows="3" placeholder="Informations supplémentaires pour votre client..."
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
                            </div>

                            <!-- Conditions -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Conditions de paiement</label>
                                <textarea name="terms" rows="2" placeholder="Conditions générales de paiement..."
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none">Paiement sous 30 jours à réception de facture.</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Récapitulatif -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        <!-- Carte récapitulatif -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-gray-900 to-gray-800">
                                <h3 class="text-lg font-semibold text-white">Récapitulatif</h3>
                                <p class="text-sm text-gray-400">Aperçu de votre facture</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Sous-total -->
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500">Sous-total</span>
                                    <span class="font-medium text-gray-900" x-text="formatCurrency(subtotal)"></span>
                                </div>

                                <!-- TVA -->
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500">TVA</span>
                                    <span class="font-medium text-gray-900" x-text="formatCurrency(taxAmount)"></span>
                                </div>

                                <!-- Remise -->
                                <div class="flex justify-between items-center py-2" x-show="discountAmount > 0">
                                    <span class="text-gray-500">Remise</span>
                                    <span class="font-medium text-red-500"
                                        x-text="'-' + formatCurrency(discountAmount)"></span>
                                </div>

                                <div class="border-t border-gray-100 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">Total</span>
                                        <span
                                            class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"
                                            x-text="formatCurrency(total)"></span>
                                    </div>
                                    <input type="hidden" name="subtotal" :value="subtotal">
                                    <input type="hidden" name="tax_amount" :value="taxAmount">
                                    <input type="hidden" name="discount_amount" :value="discountAmount">
                                    <input type="hidden" name="total" :value="total">
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-3">
                            <button type="submit" name="action" value="draft"
                                class="w-full py-4 px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                    </path>
                                </svg>
                                Sauvegarder brouillon
                            </button>

                            <button type="submit" name="action" value="send"
                                :disabled="!selectedClient || items.length === 0"
                                :class="(!selectedClient || items.length === 0) ? 'opacity-50 cursor-not-allowed' :
                                'hover:shadow-lg hover:scale-[1.02]'"
                                class="w-full py-4 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl transition-all duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Créer et envoyer
                            </button>
                        </div>

                        <!-- Aide -->
                        <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-600">
                                        <strong>Astuce :</strong> Vous pouvez sauvegarder votre facture en brouillon et la
                                        modifier plus tard.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal nouveau client -->
    <div x-data="{ open: false }" x-show="open" x-on:open-client-modal.window="open = true" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity" @click="open = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 transform transition-all"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Nouveau client</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('client.clients.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="redirect_back" value="1">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="tel" name="phone"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <textarea name="address" rows="2"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" @click="open = false"
                            class="flex-1 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function invoiceCreator() {
            return {
                step: 1,
                isSaving: false,
                lastSaved: false,

                // Client
                clientSearch: '',
                showClientDropdown: false,
                selectedClient: null,
                clients: @json($clients ?? []),
                filteredClients: [],

                // Articles
                items: [{
                    key: Date.now(),
                    product_id: '',
                    description: '',
                    quantity: 1,
                    unit_price: 0,
                    tax_rate: 18
                }],

                // Dates
                invoiceDate: new Date().toISOString().split('T')[0],
                dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],

                // Remise
                discountType: 'percentage',
                discountValue: 0,

                // Totaux
                subtotal: 0,
                taxAmount: 0,
                discountAmount: 0,
                total: 0,

                // Produits
                products: @json($products ?? []),

                init() {
                    this.filteredClients = this.clients;
                    this.calculateTotals();

                    // Auto-save toutes les 30 secondes
                    setInterval(() => {
                        this.autoSave();
                    }, 30000);
                },

                filterClients() {
                    const search = this.clientSearch.toLowerCase();
                    this.filteredClients = this.clients.filter(client =>
                        client.name.toLowerCase().includes(search) ||
                        client.email.toLowerCase().includes(search)
                    );
                },

                selectClient(client) {
                    this.selectedClient = client;
                    this.clientSearch = client.name;
                    this.showClientDropdown = false;
                    this.step = 2;
                },

                clearClient() {
                    this.selectedClient = null;
                    this.clientSearch = '';
                    this.step = 1;
                },

                openNewClientModal() {
                    window.dispatchEvent(new CustomEvent('open-client-modal'));
                },

                addItem() {
                    this.items.push({
                        key: Date.now(),
                        product_id: '',
                        description: '',
                        quantity: 1,
                        unit_price: 0,
                        tax_rate: 18
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotals();
                    }
                },

                incrementQty(index) {
                    this.items[index].quantity++;
                    this.calculateTotals();
                },

                decrementQty(index) {
                    if (this.items[index].quantity > 1) {
                        this.items[index].quantity--;
                        this.calculateTotals();
                    }
                },

                updateItemFromProduct(index) {
                    const productId = this.items[index].product_id;
                    if (productId) {
                        const product = this.products.find(p => p.id == productId);
                        if (product) {
                            this.items[index].unit_price = parseFloat(product.unit_price) || 0;
                            this.items[index].tax_rate = parseFloat(product.tax_rate) || 18;
                            this.items[index].description = product.description || product.name || '';
                        }
                    }
                    this.calculateTotals();
                },

                quickAddProduct(productId, name, price, taxRate) {
                    this.items.push({
                        key: Date.now(),
                        product_id: productId,
                        description: name,
                        quantity: 1,
                        unit_price: parseFloat(price) || 0,
                        tax_rate: parseFloat(taxRate) || 18
                    });
                    this.calculateTotals();
                },

                calculateTotals() {
                    // Sous-total (sans TVA)
                    this.subtotal = this.items.reduce((sum, item) => {
                        return sum + (item.quantity * item.unit_price);
                    }, 0);

                    // TVA
                    this.taxAmount = this.items.reduce((sum, item) => {
                        const lineTotal = item.quantity * item.unit_price;
                        return sum + (lineTotal * (item.tax_rate / 100));
                    }, 0);

                    // Remise
                    if (this.discountType === 'percentage') {
                        this.discountAmount = (this.subtotal + this.taxAmount) * (this.discountValue / 100);
                    } else {
                        this.discountAmount = this.discountValue;
                    }

                    // Total
                    this.total = this.subtotal + this.taxAmount - this.discountAmount;

                    if (this.total < 0) this.total = 0;
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'decimal',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount) + ' XOF';
                },

                autoSave() {
                    if (this.selectedClient || this.items.some(i => i.product_id)) {
                        this.isSaving = true;
                        // Simulation de sauvegarde
                        setTimeout(() => {
                            this.isSaving = false;
                            this.lastSaved = true;
                            setTimeout(() => {
                                this.lastSaved = false;
                            }, 3000);
                        }, 1000);
                    }
                },

                validateForm(e) {
                    if (!this.selectedClient) {
                        e.preventDefault();
                        alert('Veuillez sélectionner un client');
                        this.step = 1;
                        return false;
                    }

                    const hasValidItem = this.items.some(item => item.product_id || item.unit_price > 0);
                    if (!hasValidItem) {
                        e.preventDefault();
                        alert('Veuillez ajouter au moins un article');
                        this.step = 2;
                        return false;
                    }

                    // Auto-fill description from product name if empty
                    this.items.forEach(item => {
                        if (!item.description || item.description.trim() === '') {
                            if (item.product_id) {
                                const product = this.products.find(p => p.id == item.product_id);
                                if (product) {
                                    item.description = product.name || 'Article';
                                }
                            } else {
                                item.description = 'Article personnalisé';
                            }
                        }
                    });

                    // Remove items with no product and no price (empty rows)
                    this.items = this.items.filter(item => item.product_id || item.unit_price > 0 || item.description
                    .trim() !== '');

                    return true;
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* FORCER LA VISIBILITE DU TEXTE DANS LES INPUTS */
        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        textarea,
        select {
            color: #111827 !important;
            /* text-gray-900 */
            background-color: #ffffff !important;
            -webkit-text-fill-color: #111827 !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: #9ca3af !important;
            /* text-gray-400 */
            -webkit-text-fill-color: #9ca3af !important;
        }

        /* Fix pour Safari et iOS */
        input::-webkit-input-placeholder {
            color: #9ca3af !important;
        }

        input::-moz-placeholder {
            color: #9ca3af !important;
        }

        input:-ms-input-placeholder {
            color: #9ca3af !important;
        }

        /* Animation pour les cartes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Scrollbar personnalisée */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection
