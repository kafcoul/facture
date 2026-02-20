@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <!-- En-tête avec titre et actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mes Factures</h1>
                <p class="mt-1 text-sm text-gray-600">Consultez et téléchargez vos factures</p>
            </div>
            <a href="{{ route('client.invoices.create') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle facture
            </a>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" action="{{ route('client.invoices.index') }}" class="flex flex-col sm:flex-row gap-4">
                <!-- Filtre par statut -->
                <div class="flex-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" id="status"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Envoyée</option>
                        <option value="viewed" {{ request('status') === 'viewed' ? 'selected' : '' }}>Vue</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payée</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>En retard</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                <!-- Recherche -->
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="N° de facture ou client..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Boutons -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    @if (request('status') || request('search'))
                        <a href="{{ route('client.invoices.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Liste des factures -->
        @if ($invoices->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    N° Facture
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date d'échéance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($invoices as $invoice)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('client.invoices.show', $invoice) }}"
                                            class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                            {{ $invoice->number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $invoice->client->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $invoice->client->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->created_at?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->due_date?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($invoice->total, 0, ',', ' ') }} XOF
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = [
                                                'draft' => [
                                                    'label' => 'Brouillon',
                                                    'class' => 'bg-gray-100 text-gray-800',
                                                ],
                                                'sent' => [
                                                    'label' => 'Envoyée',
                                                    'class' => 'bg-blue-100 text-blue-800',
                                                ],
                                                'viewed' => [
                                                    'label' => 'Vue',
                                                    'class' => 'bg-purple-100 text-purple-800',
                                                ],
                                                'paid' => [
                                                    'label' => 'Payée',
                                                    'class' => 'bg-green-100 text-green-800',
                                                ],
                                                'overdue' => [
                                                    'label' => 'En retard',
                                                    'class' => 'bg-red-100 text-red-800',
                                                ],
                                                'cancelled' => [
                                                    'label' => 'Annulée',
                                                    'class' => 'bg-gray-100 text-gray-600',
                                                ],
                                            ];
                                            $status = $statusConfig[$invoice->status] ?? [
                                                'label' => $invoice->status,
                                                'class' => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('client.invoices.show', $invoice) }}"
                                                class="text-blue-600 hover:text-blue-900" title="Voir les détails">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('client.invoices.download', $invoice) }}"
                                                class="text-green-600 hover:text-green-900" title="Télécharger le PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $invoices->links() }}
                </div>
            </div>
        @else
            <!-- État vide -->
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune facture</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if (request('status') || request('search'))
                        Aucune facture ne correspond à vos critères de recherche.
                    @else
                        Vous n'avez pas encore de factures.
                    @endif
                </p>
                @if (request('status') || request('search'))
                    <div class="mt-6">
                        <a href="{{ route('client.invoices.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Voir toutes les factures
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
