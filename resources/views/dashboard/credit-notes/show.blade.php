@extends('layouts.dashboard')

@section('title', 'Avoir ' . $creditNote->number)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('client.credit-notes.index') }}"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $creditNote->number }}</h1>
                    <p class="mt-1 text-gray-600">Avoir —
                        {{ \App\Domain\Invoice\Models\CreditNote::REASONS[$creditNote->reason] ?? $creditNote->reason }}</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-700',
                        'issued' => 'bg-blue-100 text-blue-700',
                        'applied' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span
                    class="px-3 py-1 text-sm font-medium rounded-full {{ $statusColors[$creditNote->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ \App\Domain\Invoice\Models\CreditNote::STATUSES[$creditNote->status] ?? $creditNote->status }}
                </span>

                @if ($creditNote->status === 'draft')
                    <a href="{{ route('client.credit-notes.edit', $creditNote) }}"
                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier
                    </a>
                    <form action="{{ route('client.credit-notes.status', $creditNote) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="issued">
                        <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                            Émettre
                        </button>
                    </form>
                @endif

                @if ($creditNote->status === 'issued')
                    <form action="{{ route('client.credit-notes.status', $creditNote) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="applied">
                        <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                            Appliquer
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Info cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Client</p>
                <p class="text-lg font-semibold text-gray-900">{{ $creditNote->client->name ?? '—' }}</p>
                @if ($creditNote->client?->company)
                    <p class="text-sm text-gray-500">{{ $creditNote->client->company }}</p>
                @endif
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Facture associée</p>
                @if ($creditNote->invoice)
                    <a href="{{ route('client.invoices.show', $creditNote->invoice) }}"
                        class="text-lg font-semibold text-blue-600 hover:text-blue-800">
                        {{ $creditNote->invoice->number }}
                    </a>
                @else
                    <p class="text-lg font-semibold text-gray-400">Aucune</p>
                @endif
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Montant total</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($creditNote->total, 0, ',', ' ') }} <span
                        class="text-sm font-normal text-gray-500">XOF</span></p>
            </div>
        </div>

        <!-- Items table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Lignes de l'avoir</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qté</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prix unit.</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($creditNote->items ?? [] as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item['description'] ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $item['quantity'] ?? 0 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">
                                    {{ number_format($item['unit_price'] ?? 0, 0, ',', ' ') }} XOF</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                    {{ number_format($item['total'] ?? 0, 0, ',', ' ') }} XOF</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900 text-right">Total</td>
                            <td class="px-6 py-4 text-lg font-bold text-gray-900 text-right">
                                {{ number_format($creditNote->total, 0, ',', ' ') }} XOF</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Notes -->
        @if ($creditNote->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Notes</h3>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $creditNote->notes }}</p>
            </div>
        @endif

        <!-- Delete (draft only) -->
        @if ($creditNote->status === 'draft')
            <div class="flex justify-end">
                <form action="{{ route('client.credit-notes.destroy', $creditNote) }}" method="POST"
                    onsubmit="return confirm('Supprimer cet avoir ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer cet avoir
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
