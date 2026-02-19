@extends('layouts.dashboard')

@section('title', $product->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('client.products.index') }}" 
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center space-x-4">
                <div class="p-3 rounded-xl {{ $product->type === 'service' ? 'bg-purple-100' : 'bg-green-100' }}">
                    @if($product->type === 'service')
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    @else
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    @endif
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                        @if(!$product->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                            Inactif
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $product->type === 'service' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                            {{ $product->type === 'service' ? 'Service' : 'Produit' }}
                        </span>
                        @if($product->sku)
                        <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('client.products.edit', $product) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Price Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Tarification</h2>
                <div class="flex items-baseline space-x-2">
                    <span class="text-4xl font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }}</span>
                    <span class="text-xl text-gray-500">XOF</span>
                    @if($product->unit)
                    <span class="text-gray-500">/ {{ $product->unit }}</span>
                    @endif
                </div>
                @if($product->tax_rate)
                <p class="mt-2 text-sm text-gray-500">TVA {{ $product->tax_rate }}% incluse</p>
                @endif
            </div>

            <!-- Description -->
            @if($product->description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $product->description }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Détails</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">Type</dt>
                        <dd class="text-gray-900">{{ $product->type === 'service' ? 'Service' : 'Produit' }}</dd>
                    </div>
                    @if($product->sku)
                    <div>
                        <dt class="text-sm text-gray-500">SKU / Référence</dt>
                        <dd class="text-gray-900">{{ $product->sku }}</dd>
                    </div>
                    @endif
                    @if($product->unit)
                    <div>
                        <dt class="text-sm text-gray-500">Unité</dt>
                        <dd class="text-gray-900">{{ $product->unit }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500">Statut</dt>
                        <dd>
                            @if($product->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                Inactif
                            </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Créé le</dt>
                        <dd class="text-gray-900">{{ $product->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Dernière modification</dt>
                        <dd class="text-gray-900">{{ $product->updated_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <h2 class="text-lg font-semibold text-red-600 mb-4">Zone de danger</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Supprimer ce produit de votre catalogue. Cette action est irréversible.
                </p>
                <form action="{{ route('client.products.destroy', $product) }}" 
                      method="POST" 
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg font-medium hover:bg-red-200 transition-colors">
                        Supprimer ce produit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
