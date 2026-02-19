@extends('layouts.dashboard')

@section('title', 'Produits & Services')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Produits & Services</h1>
            <p class="mt-1 text-gray-600">Gérez votre catalogue de produits et services</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('client.products.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau produit
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Rechercher par nom, SKU ou description..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les types</option>
                    <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Produits</option>
                    <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Services</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-xl {{ $product->type === 'service' ? 'bg-purple-100' : 'bg-green-100' }}">
                            @if($product->type === 'service')
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            @endif
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $product->type === 'service' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                {{ $product->type === 'service' ? 'Service' : 'Produit' }}
                            </span>
                        </div>
                    </div>
                    @if(!$product->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                        Inactif
                    </span>
                    @endif
                </div>

                <div class="mt-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                    @if($product->sku)
                    <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                    @endif
                    @if($product->description)
                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $product->description }}</p>
                    @endif
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <span class="text-2xl font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }}</span>
                        <span class="text-sm text-gray-500">XOF</span>
                        @if($product->unit)
                        <span class="text-sm text-gray-500">/ {{ $product->unit }}</span>
                        @endif
                    </div>
                    @if($product->tax_rate)
                    <span class="text-sm text-gray-500">TVA {{ $product->tax_rate }}%</span>
                    @endif
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
                <a href="{{ route('client.products.edit', $product) }}" 
                   class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                    Modifier
                </a>
                <a href="{{ route('client.products.show', $product) }}" 
                   class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Voir
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit ou service</h3>
        <p class="text-gray-600 mb-6">Commencez par créer votre premier produit ou service</p>
        <a href="{{ route('client.products.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un produit
        </a>
    </div>
    @endif
</div>
@endsection
