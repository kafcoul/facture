@extends('layouts.dashboard')

@section('title', 'Nouvel avoir')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('client.credit-notes.index') }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nouvel avoir</h1>
                <p class="mt-1 text-gray-600">Créez une note de crédit pour un client</p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('client.credit-notes.store') }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-200" x-data="creditNoteForm()">
            @csrf

            <div class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900">Informations</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client <span
                                class="text-red-500">*</span></label>
                        <select id="client_id" name="client_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('client_id') border-red-500 @enderror">
                            <option value="">Sélectionner un client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id', $selectedInvoice?->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} {{ $client->company ? "({$client->company})" : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_id" class="block text-sm font-medium text-gray-700 mb-1">Facture
                            associée</label>
                        <select id="invoice_id" name="invoice_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Aucune (avoir libre)</option>
                            @foreach ($invoices as $invoice)
                                <option value="{{ $invoice->id }}"
                                    {{ old('invoice_id', $selectedInvoice?->id) == $invoice->id ? 'selected' : '' }}>
                                    {{ $invoice->number }} — {{ number_format($invoice->total, 0, ',', ' ') }} XOF
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Motif <span
                                class="text-red-500">*</span></label>
                        <select id="reason" name="reason" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reason') border-red-500 @enderror">
                            <option value="">Sélectionner un motif</option>
                            @foreach (\App\Domain\Invoice\Models\CreditNote::REASONS as $key => $label)
                                <option value="{{ $key }}" {{ old('reason') === $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Lignes de l'avoir</h2>
                    <button type="button" @click="addItem()"
                        class="inline-flex items-center px-3 py-1.5 text-sm bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajouter une ligne
                    </button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-3 items-end p-4 bg-gray-50 rounded-lg">
                        <div class="col-span-12 sm:col-span-5">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                            <input type="text" :name="`items[${index}][description]`" x-model="item.description" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-4 sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Quantité</label>
                            <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity"
                                step="0.01" min="0.01" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-4 sm:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Prix unitaire (XOF)</label>
                            <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price"
                                step="1" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-3 sm:col-span-1 text-right">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Total</label>
                            <p class="py-2 text-sm font-medium text-gray-900"
                                x-text="formatXOF(item.quantity * item.unit_price)"></p>
                        </div>
                        <div class="col-span-1 flex justify-end">
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Total -->
                <div class="flex justify-end pt-4">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total</span>
                            <span class="font-bold text-gray-900" x-text="formatXOF(grandTotal()) + ' XOF'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="p-6 space-y-4">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes internes</label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-6 flex justify-end gap-3">
                <a href="{{ route('client.credit-notes.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                    Créer l'avoir
                </button>
            </div>
        </form>
    </div>

    <script>
        function creditNoteForm() {
            return {
                items: [{
                    description: '',
                    quantity: 1,
                    unit_price: 0
                }],
                addItem() {
                    this.items.push({
                        description: '',
                        quantity: 1,
                        unit_price: 0
                    });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                grandTotal() {
                    return this.items.reduce((sum, i) => sum + (i.quantity * i.unit_price), 0);
                },
                formatXOF(val) {
                    return new Intl.NumberFormat('fr-FR', {
                        maximumFractionDigits: 0
                    }).format(val);
                },
            };
        }
    </script>
@endsection
