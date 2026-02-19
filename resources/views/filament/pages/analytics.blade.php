<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Sélecteur de période --}}
        <div class="flex justify-end">
            <select wire:model.live="period"
                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                <option value="3">3 derniers mois</option>
                <option value="6">6 derniers mois</option>
                <option value="12">12 derniers mois</option>
                <option value="24">24 derniers mois</option>
            </select>
        </div>

        @php $data = $this->getAnalyticsData(); @endphp

        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <x-heroicon-o-banknotes class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Chiffre d'Affaires</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($data['kpis']['totalRevenue'], 0, ',', ' ') }} XOF</p>
                        @if ($data['kpis']['revenueGrowth'] != 0)
                            <p
                                class="text-xs mt-1 {{ $data['kpis']['revenueGrowth'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $data['kpis']['revenueGrowth'] > 0 ? '↑' : '↓' }}
                                {{ abs($data['kpis']['revenueGrowth']) }}% vs période précédente
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <x-heroicon-o-document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Factures (payées/total)</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data['kpis']['paidInvoices'] }}
                            / {{ $data['kpis']['totalInvoices'] }}</p>
                        <p class="text-xs mt-1 text-gray-500">Taux: {{ $data['kpis']['paymentRate'] }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-lg">
                        <x-heroicon-o-calculator class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Facture moyenne</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($data['kpis']['avgInvoice'], 0, ',', ' ') }} XOF</p>
                        <p class="text-xs mt-1 text-gray-500">Délai paiement: {{ $data['kpis']['avgPaymentDays'] }}j</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div
                        class="p-3 {{ $data['kpis']['overdueInvoices'] > 0 ? 'bg-red-100 dark:bg-red-900' : 'bg-green-100 dark:bg-green-900' }} rounded-lg">
                        <x-heroicon-o-exclamation-triangle
                            class="w-6 h-6 {{ $data['kpis']['overdueInvoices'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Factures en retard</p>
                        <p
                            class="text-2xl font-bold {{ $data['kpis']['overdueInvoices'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $data['kpis']['overdueInvoices'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Graphique CA mensuel --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Évolution du Chiffre d'Affaires</h3>
            @if (count($data['revenueByMonth']) > 0)
                <div class="space-y-2">
                    @php
                        $maxRevenue = max(array_values($data['revenueByMonth'])) ?: 1;
                    @endphp
                    @foreach ($data['revenueByMonth'] as $month => $total)
                        <div class="flex items-center gap-3">
                            <span
                                class="text-sm text-gray-500 w-20 shrink-0">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('M Y') }}</span>
                            <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-6 overflow-hidden">
                                <div class="bg-amber-500 h-6 rounded-full flex items-center justify-end px-2"
                                    style="width: {{ ($total / $maxRevenue) * 100 }}%">
                                    <span
                                        class="text-xs text-white font-medium whitespace-nowrap">{{ number_format($total, 0, ',', ' ') }}
                                        XOF</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucune donnée pour cette période.</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Clients --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 10 Clients</h3>
                @if (count($data['topClients']) > 0)
                    <div class="space-y-3">
                        @foreach ($data['topClients'] as $index => $client)
                            @if ($client['total'] > 0)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-amber-600 w-6">{{ $index + 1 }}.</span>
                                        <span
                                            class="text-sm text-gray-700 dark:text-gray-300">{{ $client['name'] }}</span>
                                    </div>
                                    <span
                                        class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($client['total'], 0, ',', ' ') }}
                                        XOF</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Aucune donnée.</p>
                @endif
            </div>

            {{-- Top Produits --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 10 Produits</h3>
                @if (count($data['topProducts']) > 0)
                    <div class="space-y-3">
                        @foreach ($data['topProducts'] as $index => $product)
                            @if ($product['total'] > 0)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-blue-600 w-6">{{ $index + 1 }}.</span>
                                        <span
                                            class="text-sm text-gray-700 dark:text-gray-300">{{ $product['name'] }}</span>
                                        <span class="text-xs text-gray-400">({{ $product['count'] }}x)</span>
                                    </div>
                                    <span
                                        class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($product['total'], 0, ',', ' ') }}
                                        XOF</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Aucune donnée.</p>
                @endif
            </div>
        </div>

        {{-- Paiements par passerelle --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Répartition par méthode de paiement
            </h3>
            @if (count($data['paymentsByGateway']) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($data['paymentsByGateway'] as $gateway)
                        @php
                            $gatewayLabel = match ($gateway['gateway'] ?? 'other') {
                                'stripe' => 'Stripe',
                                'paypal' => 'PayPal',
                                'orange_money' => 'Orange Money',
                                'mtn_money' => 'MTN Money',
                                'wave' => 'Wave',
                                'bank_transfer' => 'Virement',
                                'cash' => 'Espèces',
                                'check' => 'Chèque',
                                default => $gateway['gateway'] ?? 'Autre',
                            };
                            $gatewayColor = match ($gateway['gateway'] ?? 'other') {
                                'stripe' => 'bg-purple-100 text-purple-800',
                                'paypal' => 'bg-blue-100 text-blue-800',
                                'orange_money' => 'bg-orange-100 text-orange-800',
                                'mtn_money' => 'bg-yellow-100 text-yellow-800',
                                'wave' => 'bg-cyan-100 text-cyan-800',
                                'bank_transfer' => 'bg-gray-100 text-gray-800',
                                'cash' => 'bg-green-100 text-green-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                            <span
                                class="inline-block px-2 py-1 text-xs font-medium rounded {{ $gatewayColor }}">{{ $gatewayLabel }}</span>
                            <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">
                                {{ number_format($gateway['total'], 0, ',', ' ') }} XOF</p>
                            <p class="text-xs text-gray-500">{{ $gateway['count'] }} transaction(s)</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucun paiement enregistré.</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>
