@extends('layouts.dashboard')

@section('title', 'Nouveau client')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('client.clients.index') }}" 
           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau client</h1>
            <p class="mt-1 text-gray-600">Ajoutez un nouveau client à votre répertoire</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('client.clients.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-200">
        @csrf
        
        <!-- Informations principales -->
        <div class="p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Informations principales</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Entreprise</label>
                    <input type="text" 
                           id="company" 
                           name="company" 
                           value="{{ old('company') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Adresse -->
        <div class="p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Adresse</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <textarea id="address" 
                              name="address" 
                              rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           value="{{ old('city') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                    <input type="text" 
                           id="country" 
                           name="country" 
                           value="{{ old('country', 'Sénégal') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Informations fiscales -->
        <div class="p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Informations fiscales</h2>
            
            <div>
                <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-1">Numéro de TVA / NINEA</label>
                <input type="text" 
                       id="tax_number" 
                       name="tax_number" 
                       value="{{ old('tax_number') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- Notes -->
        <div class="p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Notes</h2>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes internes</label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3"
                          placeholder="Notes privées concernant ce client..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="p-6 bg-gray-50 flex items-center justify-end space-x-4">
            <a href="{{ route('client.clients.index') }}" 
               class="px-4 py-2 text-gray-700 font-medium hover:text-gray-900 transition-colors">
                Annuler
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                Créer le client
            </button>
        </div>
    </form>
</div>
@endsection
