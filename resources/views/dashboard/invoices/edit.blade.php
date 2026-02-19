@extends('layouts.dashboard')

@section('title', 'Modifier la Facture ' . $invoice->number)

@section('content')
<div x-data="invoiceEditor()" x-init="init()" class="min-h-screen">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('client.invoices.show', $invoice) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Modifier la Facture
                    </h1>
                </div>
                <p class="mt-1 text-gray-500">Facture <span class="font-semibold text-gray-700">{{ $invoice->number }}</span> — 
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        @if($invoice->status === 'draft') bg-gray-100 text-gray-700
                        @elseif($invoice->status === 'sent') bg-blue-100 text-blue-700
                        @elseif($invoice->status === 'paid') bg-green-100 text-green-700
                        @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center gap-2 text-red-700 font-semibold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Erreurs de validation
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.invoices.update', $invoice) }}" method="POST" @submit="validateForm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Section Client -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-900">Client</h2>
                                <p class="text-sm text-gray-500">Modifier le client de la facture</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="relative">
                            <input type="text" 
                                   x-model="clientSearch"
                                   @focus="showClientDropdown = true"
                                   @input="filterClients()"
                                   placeholder="Rechercher un client..."
                                   class="w-full pl-12 pr-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            
                            <!-- Dropdown clients -->
                            <div x-show="showClientDropdown && filteredClients.length > 0" x-cloak
                                 @click.away="showClientDropdown = false"
                                 class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-gray-100 max-h-60 overflow-y-auto">
                                <template x-for="client in filteredClients" :key="client.id">
                                    <button type="button" @click="selectClient(client)"
                                            class="w-full px-4 py-3 text-left hover:bg-indigo-50 flex items-center space-x-3 border-b border-gray-50 last:border-0">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold"
                                             x-text="client.name.charAt(0).toUpperCase()"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate" x-text="client.name"></p>
                                            <p class="text-sm text-gray-500 truncate" x-text="client.email"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Client sélectionné -->
                        <div x-show="selectedClient" x-cloak class="mt-4">
                            <input type="hidden" name="client_id" :value="selectedClient?.id">
                            <div class="p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg"
                                             x-text="selectedClient?.name?.charAt(0).toUpperCase()"></div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900" x-text="selectedClient?.name"></h4>
                                            <p class="text-sm text-gray-600" x-text="selectedClient?.email"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="clearClient()" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Articles -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-green-600 text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Articles & Services</h2>
                                    <p class="text-sm text-gray-500">Lignes de la facture</p>
                                </div>
                            </div>
                            <button type="button" @click="addItem()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Ajouter
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <template x-for="(item, index) in items" :key="item.key">
                            <div class="mb-4 p-4 bg-gray-50 rounded-xl border border-gray-200 relative group">
                                <!-- Supprimer -->
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    ✕
                                </button>
                                
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                    <!-- Produit (optionnel) -->
                                    <div class="md:col-span-4">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Produit/Service</label>
                                        <select x-model="item.product_id" @change="updateItemFromProduct(index)"
                                                :name="'items['+index+'][product_id]'"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">— Saisie libre —</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->unit_price, 0, ',', ' ') }} XOF)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Description -->
                                    <div class="md:col-span-8">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                                        <input type="text" x-model="item.description" :name="'items['+index+'][description]'" required
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="Description de l'article">
                                    </div>
                                    
                                    <!-- Quantité -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Qté</label>
                                        <input type="number" x-model="item.quantity" :name="'items['+index+'][quantity]'" required min="0.01" step="0.01"
                                               @input="calculateTotals()"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <!-- Prix unitaire -->
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Prix unitaire (XOF)</label>
                                        <input type="number" x-model="item.unit_price" :name="'items['+index+'][unit_price]'" required min="0" step="1"
                                               @input="calculateTotals()"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <!-- TVA -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">TVA %</label>
                                        <input type="number" x-model="item.tax_rate" :name="'items['+index+'][tax_rate]'" required min="0" max="100" step="0.5"
                                               @input="calculateTotals()"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <!-- Total ligne -->
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Total HT</label>
                                        <div class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900"
                                             x-text="formatCurrency(item.quantity * item.unit_price)"></div>
                                    </div>
                                    
                                    <!-- Spacer pour alignement -->
                                    <div class="md:col-span-2"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Section Notes & Conditions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Notes & Conditions</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (visible par le client)</label>
                            <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Notes, instructions de paiement...">{{ old('notes', $invoice->notes) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Conditions</label>
                            <textarea name="terms" rows="4" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Conditions de paiement...">{{ old('terms', $invoice->terms) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <!-- Dates -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date d'émission</label>
                            <input type="date" name="issue_date" x-model="invoiceDate"
                                   class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date d'échéance</label>
                            <input type="date" name="due_date" x-model="dueDate"
                                   class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Remise -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Remise</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <select x-model="discountType" @change="calculateTotals()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="percentage">%</option>
                                <option value="fixed">Montant fixe</option>
                            </select>
                            <input type="number" x-model="discountValue" @input="calculateTotals()" min="0" step="0.01"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <input type="hidden" name="discount_percentage" :value="discountType === 'percentage' ? discountValue : 0">
                        <input type="hidden" name="discount_amount" :value="discountType === 'fixed' ? discountValue : 0">
                    </div>
                </div>

                <!-- Résumé -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Sous-total HT</span>
                            <span class="font-medium text-gray-900" x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">TVA</span>
                            <span class="font-medium text-gray-900" x-text="formatCurrency(taxAmount)"></span>
                        </div>
                        <div x-show="discountAmount > 0" class="flex justify-between text-sm">
                            <span class="text-gray-500">Remise</span>
                            <span class="font-medium text-red-600" x-text="'-' + formatCurrency(discountAmount)"></span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg">
                            <span class="font-bold text-gray-900">Total TTC</span>
                            <span class="font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent" x-text="formatCurrency(total)"></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('client.invoices.show', $invoice) }}" class="w-full py-3 px-4 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function invoiceEditor() {
    return {
        // Clients
        clients: @json($clients),
        filteredClients: [],
        selectedClient: @json($invoice->client),
        clientSearch: @json($invoice->client->name ?? ''),
        showClientDropdown: false,
        
        // Items
        items: @json($invoice->items->map(fn($item) => [
            'key' => $item->id,
            'product_id' => $item->product_id ?? '',
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'tax_rate' => $item->tax_rate,
        ])),
        
        // Dates
        invoiceDate: '{{ $invoice->issued_at?->format("Y-m-d") ?? now()->format("Y-m-d") }}',
        dueDate: '{{ $invoice->due_date?->format("Y-m-d") ?? now()->addDays(30)->format("Y-m-d") }}',
        
        // Remise
        discountType: {{ $invoice->discount_percentage > 0 ? "'percentage'" : "'fixed'" }},
        discountValue: {{ $invoice->discount_percentage > 0 ? $invoice->discount_percentage : ($invoice->discount ?? 0) }},
        
        // Totaux
        subtotal: 0,
        taxAmount: 0,
        discountAmount: 0,
        total: 0,
        
        // Produits
        products: @json($products),
        
        init() {
            this.filteredClients = this.clients;
            if (this.items.length === 0) {
                this.addItem();
            }
            this.calculateTotals();
        },
        
        filterClients() {
            const search = this.clientSearch.toLowerCase();
            this.filteredClients = this.clients.filter(c => 
                c.name.toLowerCase().includes(search) || c.email.toLowerCase().includes(search)
            );
        },
        
        selectClient(client) {
            this.selectedClient = client;
            this.clientSearch = client.name;
            this.showClientDropdown = false;
        },
        
        clearClient() {
            this.selectedClient = null;
            this.clientSearch = '';
        },
        
        addItem() {
            this.items.push({
                key: Date.now(),
                product_id: '',
                description: '',
                quantity: 1,
                unit_price: 0,
                tax_rate: 18
            });
        },
        
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
                this.calculateTotals();
            }
        },
        
        updateItemFromProduct(index) {
            const productId = this.items[index].product_id;
            if (productId) {
                const product = this.products.find(p => p.id == productId);
                if (product) {
                    this.items[index].unit_price = product.unit_price || product.price;
                    this.items[index].tax_rate = product.tax_rate || 18;
                    this.items[index].description = product.description || '';
                }
            }
            this.calculateTotals();
        },
        
        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            this.taxAmount = this.items.reduce((sum, item) => {
                return sum + (item.quantity * item.unit_price * (item.tax_rate / 100));
            }, 0);
            
            if (this.discountType === 'percentage') {
                this.discountAmount = (this.subtotal + this.taxAmount) * (this.discountValue / 100);
            } else {
                this.discountAmount = parseFloat(this.discountValue) || 0;
            }
            
            this.total = Math.max(0, this.subtotal + this.taxAmount - this.discountAmount);
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount) + ' XOF';
        },
        
        validateForm(e) {
            if (!this.selectedClient) {
                e.preventDefault();
                alert('Veuillez sélectionner un client');
                return false;
            }
            const hasValidItem = this.items.some(item => item.description && item.unit_price > 0);
            if (!hasValidItem) {
                e.preventDefault();
                alert('Veuillez ajouter au moins un article valide');
                return false;
            }
            return true;
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
input[type="text"], input[type="number"], input[type="email"], input[type="date"], textarea, select {
    color: #111827 !important;
    background-color: #ffffff !important;
}
input::placeholder, textarea::placeholder {
    color: #9ca3af !important;
}
</style>
@endsection
