@extends('layouts.dashboard')

@section('title', 'Facture récurrente — ' . ($recurringInvoice->client->name ?? ''))

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('client.recurring-invoices.index') }}"
                    class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-gray-900 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Facture récurrente</h1>
                    <p class="mt-1 text-gray-600">{{ $recurringInvoice->client->name ?? '—' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Toggle active/inactive -->
                <form method="POST" action="{{ route('client.recurring-invoices.toggle', $recurringInvoice) }}"
                    class="inline">
                    @csrf
                    @if ($recurringInvoice->is_active)
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-orange-700 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition-colors"
                            onclick="return confirm('Désactiver cette facturation récurrente ?')">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Désactiver
                        </button>
                    @else
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Activer
                        </button>
                    @endif
                </form>

                <a href="{{ route('client.recurring-invoices.edit', $recurringInvoice) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>

                <form method="POST" action="{{ route('client.recurring-invoices.destroy', $recurringInvoice) }}"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors"
                        onclick="return confirm('Supprimer cette facturation récurrente ? Cette action est irréversible.')">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <!-- Success message -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Status & Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                @if ($recurringInvoice->is_active)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
                        Active
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                        <span class="w-2 h-2 mr-2 bg-gray-400 rounded-full"></span>
                        Inactive
                    </span>
                @endif
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ \App\Domain\Invoice\Models\RecurringInvoice::FREQUENCIES[$recurringInvoice->frequency] ?? $recurringInvoice->frequency }}
                </span>
                @if ($recurringInvoice->auto_send)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        Envoi auto
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $recurringInvoice->client->name ?? '—' }}</dd>
                    <dd class="text-xs text-gray-500">{{ $recurringInvoice->client->email ?? '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date de début</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $recurringInvoice->start_date->format('d/m/Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date de fin</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        {{ $recurringInvoice->end_date ? $recurringInvoice->end_date->format('d/m/Y') : 'Illimitée' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Prochaine échéance</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        @if ($recurringInvoice->next_due_date)
                            {{ $recurringInvoice->next_due_date->format('d/m/Y') }}
                            @if ($recurringInvoice->next_due_date->isPast())
                                <span class="text-red-500 text-xs">(échue)</span>
                            @endif
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Générations</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        {{ $recurringInvoice->occurrences_count }}
                        @if ($recurringInvoice->occurrences_limit)
                            / {{ $recurringInvoice->occurrences_limit }}
                        @else
                            <span class="text-gray-500">(illimité)</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière génération</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        {{ $recurringInvoice->last_generated_at ? $recurringInvoice->last_generated_at->format('d/m/Y H:i') : 'Jamais' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Montant par facture</dt>
                    <dd class="mt-1 text-lg font-bold text-blue-600">
                        {{ number_format($recurringInvoice->total, 0, ',', ' ') }} XOF</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Créée le</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $recurringInvoice->created_at->format('d/m/Y') }}
                    </dd>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Lignes de facturation</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantité</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix
                                unitaire</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($recurringInvoice->items ?? [] as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item['description'] ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $item['quantity'] ?? 0 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">
                                    {{ number_format($item['unit_price'] ?? 0, 0, ',', ' ') }} XOF</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($item['total'] ?? ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 0, ',', ' ') }}
                                    XOF</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Aucune ligne</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-600">Sous-total
                            </td>
                            <td class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                                {{ number_format($recurringInvoice->subtotal, 0, ',', ' ') }} XOF</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-600">Taxe</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-500">
                                {{ number_format($recurringInvoice->tax, 0, ',', ' ') }} XOF</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-base font-bold text-gray-900">Total</td>
                            <td class="px-6 py-3 text-right text-base font-bold text-blue-600">
                                {{ number_format($recurringInvoice->total, 0, ',', ' ') }} XOF</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Notes & Terms -->
        @if ($recurringInvoice->notes || $recurringInvoice->terms)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if ($recurringInvoice->notes)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Notes</h3>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $recurringInvoice->notes }}</p>
                        </div>
                    @endif
                    @if ($recurringInvoice->terms)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Conditions</h3>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $recurringInvoice->terms }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Generated Invoices -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Factures générées</h2>
            </div>
            @if ($recurringInvoice->invoices && $recurringInvoice->invoices->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Numéro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($recurringInvoice->invoices as $invoice)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                        {{ $invoice->invoice_number ?? ($invoice->number ?? '—') }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">
                                        {{ $invoice->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3 text-sm font-semibold text-gray-900">
                                        {{ number_format($invoice->total, 0, ',', ' ') }} XOF</td>
                                    <td class="px-6 py-3">
                                        @php
                                            $colors = [
                                                'draft' => 'bg-gray-100 text-gray-700',
                                                'sent' => 'bg-blue-100 text-blue-700',
                                                'paid' => 'bg-green-100 text-green-700',
                                                'overdue' => 'bg-red-100 text-red-700',
                                                'cancelled' => 'bg-red-100 text-red-600',
                                            ];
                                            $labels = [
                                                'draft' => 'Brouillon',
                                                'sent' => 'Envoyée',
                                                'paid' => 'Payée',
                                                'overdue' => 'En retard',
                                                'cancelled' => 'Annulée',
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$invoice->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $labels[$invoice->status] ?? $invoice->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('client.invoices.show', $invoice) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                            Voir →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500">Aucune facture générée pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
