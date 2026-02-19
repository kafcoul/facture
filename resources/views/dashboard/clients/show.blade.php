@extends('layouts.dashboard')

@section('title', $client->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('client.clients.index') }}" 
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="text-xl font-bold text-purple-600">{{ strtoupper(substr($client->name, 0, 2)) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h1>
                    @if($client->company)
                    <p class="text-gray-600">{{ $client->company }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('client.invoices.create') }}?client_id={{ $client->id }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle facture
            </a>
            <a href="{{ route('client.clients.edit', $client) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $client->invoices_count ?? 0 }}</h3>
                <p class="text-sm text-gray-600">Factures</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($client->invoices_sum_total ?? 0, 0, ',', ' ') }} <span class="text-base font-normal text-gray-500">XOF</span></h3>
                <p class="text-sm text-gray-600">Chiffre d'affaires</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-purple-100 rounded-xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $client->created_at->format('d/m/Y') }}</h3>
                <p class="text-sm text-gray-600">Client depuis</p>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informations</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <a href="mailto:{{ $client->email }}" class="text-blue-600 hover:text-blue-700">{{ $client->email }}</a>
                </div>
                @if($client->phone)
                <div>
                    <p class="text-sm text-gray-500">Téléphone</p>
                    <a href="tel:{{ $client->phone }}" class="text-gray-900 hover:text-blue-600">{{ $client->phone }}</a>
                </div>
                @endif
                @if($client->address || $client->city || $client->country)
                <div>
                    <p class="text-sm text-gray-500">Adresse</p>
                    <p class="text-gray-900">
                        @if($client->address){{ $client->address }}<br>@endif
                        @if($client->city){{ $client->city }}@endif
                        @if($client->country), {{ $client->country }}@endif
                    </p>
                </div>
                @endif
                @if($client->tax_number)
                <div>
                    <p class="text-sm text-gray-500">Numéro fiscal</p>
                    <p class="text-gray-900">{{ $client->tax_number }}</p>
                </div>
                @endif
                @if($client->notes)
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Notes</p>
                    <p class="text-gray-900">{{ $client->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Factures récentes</h2>
                    <a href="{{ route('client.invoices.index') }}?client={{ $client->id }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Voir tout →
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentInvoices as $invoice)
                <a href="{{ route('client.invoices.show', $invoice) }}" 
                   class="block p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                <p class="text-sm text-gray-500">{{ $invoice->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} XOF</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($invoice->status === 'paid') bg-green-100 text-green-800
                                @elseif($invoice->status === 'pending') bg-amber-100 text-amber-800
                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($invoice->status === 'paid') Payée
                                @elseif($invoice->status === 'pending') En attente
                                @elseif($invoice->status === 'overdue') En retard
                                @else {{ ucfirst($invoice->status) }}
                                @endif
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center">
                    <p class="text-gray-500">Aucune facture pour ce client</p>
                    <a href="{{ route('client.invoices.create') }}?client_id={{ $client->id }}" 
                       class="inline-flex items-center mt-4 text-blue-600 hover:text-blue-700 font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer une facture
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-xl shadow-sm border border-red-200">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-red-600 mb-4">Zone de danger</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-900 font-medium">Supprimer ce client</p>
                    <p class="text-sm text-gray-500">Cette action est irréversible. Les factures associées ne seront pas supprimées.</p>
                </div>
                <form action="{{ route('client.clients.destroy', $client) }}" 
                      method="POST" 
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg font-medium hover:bg-red-200 transition-colors">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
