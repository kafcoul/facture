@extends('layouts.dashboard')

@section('title', 'Modifier la facture récurrente')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('client.recurring-invoices.show', $recurringInvoice) }}"
                class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-gray-900 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Retour
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifier la facture récurrente</h1>
                <p class="mt-1 text-gray-600">{{ $recurringInvoice->client->name ?? '' }}</p>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('client.recurring-invoices.update', $recurringInvoice) }}"
            x-data="recurringForm()">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Client & Frequency -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Client -->
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client <span
                                    class="text-red-500">*</span></label>
                            <select name="client_id" id="client_id"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('client_id') border-red-500 @enderror">
                                <option value="">Sélectionner un client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ old('client_id', $recurringInvoice->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Frequency -->
                        <div>
                            <label for="frequency" class="block text-sm font-medium text-gray-700 mb-1">Fréquence <span
                                    class="text-red-500">*</span></label>
                            <select name="frequency" id="frequency"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('frequency') border-red-500 @enderror">
                                @foreach ($frequencies as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('frequency', $recurringInvoice->frequency) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('frequency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Date (read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" value="{{ $recurringInvoice->start_date->format('Y-m-d') }}" disabled
                                class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-400">Non modifiable après création</p>
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin <span
                                    class="text-gray-400">(optionnel)</span></label>
                            <input type="date" name="end_date" id="end_date"
                                value="{{ old('end_date', $recurringInvoice->end_date ? $recurringInvoice->end_date->format('Y-m-d') : '') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Occurrences Limit -->
                        <div>
                            <label for="occurrences_limit" class="block text-sm font-medium text-gray-700 mb-1">Nombre max
                                de générations <span class="text-gray-400">(optionnel)</span></label>
                            <input type="number" name="occurrences_limit" id="occurrences_limit"
                                value="{{ old('occurrences_limit', $recurringInvoice->occurrences_limit) }}" min="1"
                                placeholder="Illimité si vide"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('occurrences_limit') border-red-500 @enderror">
                            @error('occurrences_limit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if ($recurringInvoice->occurrences_count > 0)
                                <p class="mt-1 text-xs text-gray-500">{{ $recurringInvoice->occurrences_count }} facture(s)
                                    déjà générée(s)</p>
                            @endif
                        </div>

                        <!-- Auto Send -->
                        <div class="flex items-center pt-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="auto_send" value="1"
                                    {{ old('auto_send', $recurringInvoice->auto_send) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-700">Envoyer automatiquement au
                                    client</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Lignes de facturation</h2>
                        <button type="button" @click="addItem()"
                            class="inline-flex items-center px-3 py-1.5 text-sm bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter une ligne
                        </button>
                    </div>

                    @error('items')
                        <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex flex-col sm:flex-row gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                                    <input type="text" :name="'items[' + index + '][description]'"
                                        x-model="item.description"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        placeholder="Description du service" required>
                                </div>
                                <div class="w-full sm:w-28">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Quantité</label>
                                    <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        min="0.01" step="0.01" required>
                                </div>
                                <div class="w-full sm:w-36">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Prix unitaire (XOF)</label>
                                    <input type="number" :name="'items[' + index + '][unit_price]'"
                                        x-model="item.unit_price"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        min="0" step="1" required>
                                </div>
                                <div class="w-full sm:w-32 flex flex-col">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Total</label>
                                    <div class="flex-1 flex items-center text-sm font-semibold text-gray-900">
                                        <span x-text="formatXOF(item.quantity * item.unit_price)"></span>
                                    </div>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Totals -->
                    <div class="mt-6 flex justify-end">
                        <div class="w-full sm:w-72 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Sous-total</span>
                                <span class="font-semibold text-gray-900" x-text="formatXOF(subtotal())"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Taxe</span>
                                <span class="text-gray-500">0 XOF</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 flex justify-between text-base">
                                <span class="font-bold text-gray-900">Total</span>
                                <span class="font-bold text-blue-600" x-text="formatXOF(subtotal())"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes & Terms -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes & conditions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="Notes internes ou pour le client">{{ old('notes', $recurringInvoice->notes) }}</textarea>
                        </div>
                        <div>
                            <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">Conditions</label>
                            <textarea name="terms" id="terms" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="Conditions de paiement">{{ old('terms', $recurringInvoice->terms) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('client.recurring-invoices.show', $recurringInvoice) }}"
                        class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>

    @php
        $editItems = old('items', $recurringInvoice->items ?? [['description' => '', 'quantity' => 1, 'unit_price' => 0]]);
    @endphp
    <script>
        function recurringForm() {
            return {
                items: @json($editItems),
                addItem() {
                    this.items.push({
                        description: '',
                        quantity: 1,
                        unit_price: 0
                    });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                subtotal() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity) || 0) * (parseFloat(item
                        .unit_price) || 0), 0);
                },
                formatXOF(amount) {
                    return new Intl.NumberFormat('fr-FR', {
                        maximumFractionDigits: 0
                    }).format(amount) + ' XOF';
                }
            };
        }
    </script>
@endsection
