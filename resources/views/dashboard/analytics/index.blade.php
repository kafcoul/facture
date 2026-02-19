@extends('layouts.dashboard')

@section('title', 'Analytiques')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Analytiques</h1>
            <p class="mt-1 text-gray-600">Suivez les performances de votre activité</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <form method="GET" class="flex items-center space-x-2">
                <select name="period" onchange="this.form.submit()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="7" {{ $period == '7' ? 'selected' : '' }}>7 derniers jours</option>
                    <option value="30" {{ $period == '30' ? 'selected' : '' }}>30 derniers jours</option>
                    <option value="90" {{ $period == '90' ? 'selected' : '' }}>3 derniers mois</option>
                    <option value="365" {{ $period == '365' ? 'selected' : '' }}>Cette année</option>
                    <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Tout le temps</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Revenue Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Chiffre d'affaires -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if($revenueStats['percentage_change'] != 0)
                <span class="text-sm font-medium px-2 py-1 rounded-full {{ $revenueStats['percentage_change'] > 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }}">
                    {{ $revenueStats['percentage_change'] > 0 ? '+' : '' }}{{ $revenueStats['percentage_change'] }}%
                </span>
                @endif
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($revenueStats['total'], 0, ',', ' ') }} <span class="text-base font-normal text-gray-500">XOF</span></h3>
                <p class="text-sm text-gray-600">Chiffre d'affaires</p>
            </div>
        </div>

        <!-- Factures -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $invoiceStats['total'] }}</h3>
                <p class="text-sm text-gray-600">Factures créées</p>
            </div>
        </div>

        <!-- Taux de paiement -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-purple-100 rounded-xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $invoiceStats['payment_rate'] }}%</h3>
                <p class="text-sm text-gray-600">Taux de paiement</p>
            </div>
        </div>

        <!-- Délai moyen -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-amber-100 rounded-xl">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-gray-900">{{ $invoiceStats['avg_payment_days'] }} <span class="text-base font-normal text-gray-500">jours</span></h3>
                <p class="text-sm text-gray-600">Délai moyen de paiement</p>
            </div>
        </div>
    </div>

    <!-- Charts & Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Évolution du chiffre d'affaires</h2>
            <div class="h-64" x-data="revenueChart()" x-init="init()">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Invoice Status Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Répartition des factures</h2>
            <div class="space-y-4">
                @php
                    $statusLabels = [
                        'paid' => ['label' => 'Payées', 'color' => 'green'],
                        'pending' => ['label' => 'En attente', 'color' => 'amber'],
                        'sent' => ['label' => 'Envoyées', 'color' => 'blue'],
                        'draft' => ['label' => 'Brouillons', 'color' => 'gray'],
                        'overdue' => ['label' => 'En retard', 'color' => 'red'],
                    ];
                @endphp
                @foreach($invoiceStatusData as $status => $data)
                @php
                    $info = $statusLabels[$status] ?? ['label' => ucfirst($status), 'color' => 'gray'];
                    $percentage = $invoiceStats['total'] > 0 ? ($data['count'] / $invoiceStats['total']) * 100 : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $info['label'] }}</span>
                        <span class="text-sm text-gray-500">{{ $data['count'] }} ({{ number_format($data['total'], 0, ',', ' ') }} XOF)</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $info['color'] }}-500 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- More Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Clients -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Meilleurs clients</h2>
            </div>
            @if($topClients->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($topClients as $index => $client)
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full 
                            {{ $index === 0 ? 'bg-amber-100 text-amber-600' : ($index === 1 ? 'bg-gray-200 text-gray-600' : 'bg-amber-50 text-amber-500') }} 
                            font-semibold text-sm">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-purple-600">{{ strtoupper(substr($client->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $client->name }}</p>
                            @if($client->company)
                            <p class="text-sm text-gray-500">{{ $client->company }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">{{ number_format($client->invoices_sum_total, 0, ',', ' ') }} XOF</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center text-gray-500">
                Aucun client avec des factures payées
            </div>
            @endif
        </div>

        <!-- Client Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Clients</h2>
            <div class="space-y-6">
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <p class="text-3xl font-bold text-gray-900">{{ $clientStats['total'] }}</p>
                    <p class="text-sm text-gray-600">Clients totaux</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <p class="text-2xl font-bold text-green-600">{{ $clientStats['new'] }}</p>
                        <p class="text-xs text-gray-600">Nouveaux</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-xl">
                        <p class="text-2xl font-bold text-blue-600">{{ $clientStats['active'] }}</p>
                        <p class="text-xs text-gray-600">Actifs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    @if($paymentStats['methods']->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Méthodes de paiement</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($paymentStats['methods'] as $method => $data)
            <div class="p-4 bg-gray-50 rounded-xl text-center">
                <p class="text-lg font-bold text-gray-900">{{ number_format($data['total'], 0, ',', ' ') }} XOF</p>
                <p class="text-sm text-gray-600">{{ ucfirst($method ?: 'Autre') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $data['count'] }} paiement(s)</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function revenueChart() {
    return {
        init() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json(collect($revenueChartData)->pluck('month')),
                    datasets: [{
                        label: 'Chiffre d\'affaires',
                        data: @json(collect($revenueChartData)->pluck('revenue')),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('fr-FR').format(context.raw) + ' XOF';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    }
}
</script>
@endsection
