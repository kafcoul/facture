@extends('layouts.dashboard')

@section('content')
    <div x-data="{ showStatusModal: false, showDeleteModal: false }" class="space-y-6">
        <!-- En-tête avec retour et actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('client.invoices.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Facture {{ $invoice->number }}</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Créée le {{ $invoice->created_at?->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                {{-- Modifier (sauf si payée ou annulée) --}}
                @if (!in_array($invoice->status, ['paid', 'cancelled']))
                    <a href="{{ route('client.invoices.edit', $invoice) }}"
                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier
                    </a>
                @endif

                {{-- Envoyer par email (brouillon ou renvoi) --}}
                @if (in_array($invoice->status, ['draft', 'sent', 'viewed', 'overdue']))
                    <form method="POST" action="{{ route('client.invoices.send', $invoice) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $invoice->status === 'draft' ? 'Envoyer' : 'Renvoyer' }}
                        </button>
                    </form>
                @endif

                {{-- Dupliquer --}}
                <form method="POST" action="{{ route('client.invoices.duplicate', $invoice) }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Dupliquer
                    </button>
                </form>

                {{-- Changer le statut --}}
                @if ($invoice->status !== 'paid')
                    <button @click="showStatusModal = true"
                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Statut
                    </button>
                @endif

                {{-- Télécharger PDF --}}
                <a href="{{ route('client.invoices.download', $invoice) }}"
                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF
                </a>

                {{-- Lien public --}}
                @if ($invoice->uuid)
                    <a href="{{ route('invoices.public', $invoice->uuid) }}" target="_blank"
                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Lien
                    </a>
                @endif

                {{-- Supprimer (brouillon seulement) --}}
                @if ($invoice->status === 'draft')
                    <button @click="showDeleteModal = true"
                        class="inline-flex items-center px-3 py-2 bg-white border border-red-300 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer
                    </button>
                @endif
            </div>
        </div>

        <!-- Statut de la facture -->
        <div class="bg-white rounded-lg shadow p-6">
            @php
                $statusConfig = [
                    'draft' => [
                        'label' => 'Brouillon',
                        'class' => 'bg-gray-100 text-gray-800',
                        'icon' =>
                            'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                    ],
                    'sent' => [
                        'label' => 'Envoyée',
                        'class' => 'bg-blue-100 text-blue-800',
                        'icon' =>
                            'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    ],
                    'viewed' => [
                        'label' => 'Vue par le client',
                        'class' => 'bg-purple-100 text-purple-800',
                        'icon' =>
                            'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                    ],
                    'paid' => [
                        'label' => 'Payée',
                        'class' => 'bg-green-100 text-green-800',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    'overdue' => [
                        'label' => 'En retard',
                        'class' => 'bg-red-100 text-red-800',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    'cancelled' => [
                        'label' => 'Annulée',
                        'class' => 'bg-gray-100 text-gray-600',
                        'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                ];
                $status = $statusConfig[$invoice->status] ?? [
                    'label' => $invoice->status,
                    'class' => 'bg-gray-100 text-gray-800',
                    'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                ];
            @endphp
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 {{ str_replace('bg-', 'text-', explode(' ', $status['class'])[0]) }}"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="{{ $status['icon'] }}" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Statut de la facture</h3>
                    <span
                        class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations du client -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du client</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nom</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $invoice->client->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $invoice->client->email }}</p>
                        </div>
                        @if ($invoice->client->phone)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $invoice->client->phone }}</p>
                            </div>
                        @endif
                        @if ($invoice->client->address)
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-500">Adresse</p>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $invoice->client->address }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Articles de la facture -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Articles</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantité
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prix unitaire
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($invoice->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $item->description }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 text-center">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                            {{ number_format($item->unit_price, 0, ',', ' ') }} XOF
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                            {{ number_format($item->total, 0, ',', ' ') }} XOF
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                @if ($invoice->notes)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Notes</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Récapitulatif -->
            <div class="space-y-6">
                <!-- Dates importantes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dates importantes</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de la facture</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->created_at?->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'échéance</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->due_date->format('d/m/Y') }}</dd>
                        </div>
                        @if ($invoice->paid_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de paiement</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $invoice->paid_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Totaux -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Récapitulatif</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Sous-total</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ number_format($invoice->subtotal, 0, ',', ' ') }} XOF</dd>
                        </div>
                        @if ($invoice->tax_amount > 0)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">TVA ({{ $invoice->tax_rate }}%)</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ number_format($invoice->tax_amount, 0, ',', ' ') }} XOF</dd>
                            </div>
                        @endif
                        @if ($invoice->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <dt class="text-sm">Remise</dt>
                                <dd class="text-sm font-medium">
                                    -{{ number_format($invoice->discount_amount, 0, ',', ' ') }} XOF</dd>
                            </div>
                        @endif
                        <div class="pt-3 border-t border-gray-200 flex justify-between">
                            <dt class="text-base font-medium text-gray-900">Total</dt>
                            <dd class="text-base font-bold text-gray-900">
                                {{ number_format($invoice->total, 0, ',', ' ') }} XOF</dd>
                        </div>
                    </dl>
                </div>

                <!-- Historique des paiements -->
                @if ($invoice->payments->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Paiements reçus</h3>
                        <div class="space-y-3">
                            @foreach ($invoice->payments as $payment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if ($payment->status === 'completed')
                                                <svg class="w-5 h-5 text-green-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ number_format($payment->amount, 0, ',', ' ') }} XOF
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $payment->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500 uppercase">
                                        {{ $payment->gateway }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal: Changer le statut --}}
        <div x-show="showStatusModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-600/75" @click="showStatusModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full mx-4 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Changer le statut</h3>
                <div class="space-y-2">
                    @php
                        $transitions = [
                            'draft' => ['sent' => 'Marquer envoyée', 'cancelled' => 'Annuler'],
                            'sent' => [
                                'viewed' => 'Marquer vue',
                                'paid' => 'Marquer payée',
                                'overdue' => 'Marquer en retard',
                                'cancelled' => 'Annuler',
                            ],
                            'viewed' => [
                                'paid' => 'Marquer payée',
                                'overdue' => 'Marquer en retard',
                                'cancelled' => 'Annuler',
                            ],
                            'overdue' => ['paid' => 'Marquer payée', 'sent' => 'Renvoyer', 'cancelled' => 'Annuler'],
                            'cancelled' => ['draft' => 'Remettre en brouillon'],
                        ];
                        $available = $transitions[$invoice->status] ?? [];
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800 hover:bg-gray-200',
                            'sent' => 'bg-blue-100 text-blue-800 hover:bg-blue-200',
                            'viewed' => 'bg-purple-100 text-purple-800 hover:bg-purple-200',
                            'paid' => 'bg-green-100 text-green-800 hover:bg-green-200',
                            'overdue' => 'bg-red-100 text-red-800 hover:bg-red-200',
                            'cancelled' => 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                        ];
                    @endphp
                    @forelse($available as $newStatus => $label)
                        <form method="POST" action="{{ route('client.invoices.status', $invoice) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $newStatus }}">
                            <button type="submit"
                                class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium transition-colors {{ $statusColors[$newStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $label }}
                            </button>
                        </form>
                    @empty
                        <p class="text-sm text-gray-500 py-2">Aucune transition disponible pour ce statut.</p>
                    @endforelse
                </div>
                <button @click="showStatusModal = false"
                    class="mt-4 w-full py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200">
                    Annuler
                </button>
            </div>
        </div>

        {{-- Modal: Confirmer suppression --}}
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-600/75" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full mx-4 p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Supprimer cette facture ?</h3>
                <p class="text-sm text-gray-500 mb-6">La facture {{ $invoice->number }} sera définitivement supprimée.
                    Cette action est irréversible.</p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false"
                        class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200">
                        Annuler
                    </button>
                    <form method="POST" action="{{ route('client.invoices.destroy', $invoice) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
