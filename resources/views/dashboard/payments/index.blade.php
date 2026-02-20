@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <!-- En-tête -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes Paiements</h1>
            <p class="mt-1 text-sm text-gray-600">Historique de vos paiements</p>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" action="{{ route('client.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <!-- Filtre par statut -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" id="status"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complété
                        </option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échoué</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Remboursé</option>
                    </select>
                </div>

                <!-- Date de début -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Date de fin -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Boutons -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Filtrer
                    </button>
                    @if (request()->hasAny(['status', 'date_from', 'date_to']))
                        <a href="{{ route('client.payments.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Liste des paiements -->
        @if ($payments->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Facture
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Passerelle
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('client.invoices.show', $payment->invoice) }}"
                                            class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                            {{ $payment->invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $payment->invoice->client->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($payment->amount, 0, ',', ' ') }} XOF
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600 uppercase">{{ $payment->gateway }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = [
                                                'pending' => [
                                                    'label' => 'En attente',
                                                    'class' => 'bg-yellow-100 text-yellow-800',
                                                ],
                                                'completed' => [
                                                    'label' => 'Complété',
                                                    'class' => 'bg-green-100 text-green-800',
                                                ],
                                                'failed' => ['label' => 'Échoué', 'class' => 'bg-red-100 text-red-800'],
                                                'refunded' => [
                                                    'label' => 'Remboursé',
                                                    'class' => 'bg-gray-100 text-gray-800',
                                                ],
                                            ];
                                            $status = $statusConfig[$payment->status] ?? [
                                                'label' => $payment->status,
                                                'class' => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $payments->links() }}
                </div>
            </div>
        @else
            <!-- État vide -->
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun paiement</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if (request()->hasAny(['status', 'date_from', 'date_to']))
                        Aucun paiement ne correspond à vos critères.
                    @else
                        Vous n'avez pas encore de paiements.
                    @endif
                </p>
            </div>
        @endif
    </div>
@endsection
