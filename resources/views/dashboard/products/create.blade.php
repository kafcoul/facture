@extends('layouts.dashboard')

@section('title', 'Nouveau produit')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('client.products.index') }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nouveau produit</h1>
                <p class="mt-1 text-gray-600">Ajoutez un produit ou service à votre catalogue</p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('client.products.store') }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-200">
            @csrf

            <!-- Type -->
            <div class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900">Type</h2>

                <div class="grid grid-cols-2 gap-4" x-data="{ type: '{{ old('type', 'product') }}' }">
                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                        :class="{ 'border-blue-500 ring-2 ring-blue-500': type === 'product', 'border-gray-300': type !== 'product' }">
                        <input type="radio" name="type" value="product" class="sr-only" x-model="type">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span class="block text-sm font-medium text-gray-900">Produit</span>
                                </span>
                                <span class="mt-1 text-xs text-gray-500">Article physique ou numérique</span>
                            </span>
                        </span>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                        :class="{ 'border-blue-500 ring-2 ring-blue-500': type === 'service', 'border-gray-300': type !== 'service' }">
                        <input type="radio" name="type" value="service" class="sr-only" x-model="type">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="block text-sm font-medium text-gray-900">Service</span>
                                </span>
                                <span class="mt-1 text-xs text-gray-500">Prestation ou consultation</span>
                            </span>
                        </span>
                    </label>
                </div>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informations principales -->
            <div class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900">Informations</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Ex: Consultation stratégique"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU / Référence</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                            placeholder="Ex: CONS-001"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit') }}"
                            placeholder="Ex: heure, jour, unité"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                            placeholder="Description détaillée du produit ou service..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Tarification -->
            <div class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900">Tarification</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                            Prix <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="price" name="price" value="{{ old('price') }}"
                                step="1" min="0" placeholder="0"
                                class="w-full pl-4 pr-16 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
                                required>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">XOF</span>
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-1">Taux de TVA</label>
                        <div class="relative">
                            <input type="number" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', 18) }}"
                                step="0.1" min="0" max="100"
                                class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Statut</h2>
                        <p class="text-sm text-gray-500">Rendre ce produit disponible à la facturation</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Actif</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-6 bg-gray-50 flex items-center justify-end space-x-4">
                <a href="{{ route('client.products.index') }}"
                    class="px-4 py-2 text-gray-700 font-medium hover:text-gray-900 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                    Créer le produit
                </button>
            </div>
        </form>
    </div>
@endsection
