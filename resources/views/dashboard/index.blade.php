@extends('layouts.dashboard')

@section('title', 'Tableau de bord')

@section('content')
    <div x-data="{ showUpgradeModal: false, currentFeature: '' }" class="space-y-6">

        @php
            $user = auth()->user();
            $plan = $user->plan ?? 'starter';
            $trialEndsAt = $user->trial_ends_at ? \Carbon\Carbon::parse($user->trial_ends_at) : null;
            $isOnTrial = $trialEndsAt && $trialEndsAt->isFuture();
            $daysLeft = $isOnTrial ? now()->diffInDays($trialEndsAt) : 0;
            $planNames = ['starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'];
            $planColors = ['starter' => 'blue', 'pro' => 'purple', 'enterprise' => 'amber'];
            $currentPlanName = $planNames[$plan] ?? 'Starter';
            $planColor = $planColors[$plan] ?? 'blue';

            $totalInvoices = $stats['invoices_count'] ?? 0;
            $paidCount = $stats['paid_count'] ?? 0;
            $pendingCount = $stats['pending_count'] ?? 0;
            $overdueCount = $stats['overdue_count'] ?? 0;
            $paidPercentage = $totalInvoices > 0 ? round(($paidCount / $totalInvoices) * 100) : 0;
        @endphp

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium flex-1">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-red-800 font-medium flex-1">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Header with Greeting --}}
        <div
            class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-2xl p-8 text-white">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold">
                            @php
                                $hour = now()->hour;
                                if ($hour < 12) {
                                    echo 'Bonjour, ';
                                } elseif ($hour < 18) {
                                    echo 'Bon après-midi, ';
                                } else {
                                    echo 'Bonsoir, ';
                                }
                            @endphp
                            {{ $user->name }} 👋
                        </h1>
                        <p class="text-white/80 mt-1">Voici un aperçu de votre activité</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                            Plan {{ $currentPlanName }}
                        </span>
                    </div>
                </div>

                @if ($isOnTrial)
                    <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl">
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ $daysLeft }} jours d'essai restants</span>
                        <a href="{{ route('client.settings.index') }}"
                            class="ml-2 px-3 py-1 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-indigo-50 text-sm transition">
                            Mettre à niveau
                        </a>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <a href="{{ route('client.invoices.create') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouvelle facture
                    </a>
                    <a href="{{ route('client.invoices.index') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-xl hover:bg-white/30 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Mes factures
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            {{-- Total Factures --}}
            <a href="{{ route('client.invoices.index') }}"
                class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Total</span>
                </div>
                <p class="text-3xl lg:text-4xl font-black text-gray-900">{{ $totalInvoices }}</p>
                <p class="text-sm text-gray-500 mt-1">Factures</p>
                <p class="text-xs text-blue-600 mt-2 font-medium">{{ $stats['invoices_this_month'] ?? 0 }} ce mois-ci</p>
            </a>

            {{-- Montant Total --}}
            <div
                class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">Payé</span>
                </div>
                <p class="text-3xl lg:text-4xl font-black text-gray-900">
                    {{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }}</p>
                <p class="text-sm text-gray-500 mt-1">XOF encaissés</p>
                <p class="text-xs text-green-600 mt-2 font-medium">{{ $paidPercentage }}% de paiement</p>
            </div>

            {{-- En attente --}}
            <div
                class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">En attente</span>
                </div>
                <p class="text-3xl lg:text-4xl font-black text-gray-900">{{ $pendingCount }}</p>
                <p class="text-sm text-gray-500 mt-1">Factures en attente</p>
            </div>

            {{-- En retard --}}
            <div
                class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    @if ($overdueCount > 0)
                        <span
                            class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full animate-pulse">Urgent</span>
                    @else
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">OK</span>
                    @endif
                </div>
                <p class="text-3xl lg:text-4xl font-black text-gray-900">{{ $overdueCount }}</p>
                <p class="text-sm text-gray-500 mt-1">Factures en retard</p>
            </div>
        </div>

        {{-- Secondary Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-3">Taux de paiement</h3>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-gray-900">{{ $stats['payment_rate'] ?? 0 }}%</span>
                </div>
                <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-2 rounded-full transition-all duration-500"
                        style="width: {{ min($stats['payment_rate'] ?? 0, 100) }}%"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-3">Total clients</h3>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-gray-900">{{ $stats['total_clients'] ?? 0 }}</span>
                </div>
                <p class="text-sm text-gray-500 mt-2">Clients enregistrés</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-3">Délai moyen de paiement</h3>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-gray-900">{{ $stats['avg_payment_days'] ?? 0 }}</span>
                    <span class="text-lg text-gray-500 mb-1">jours</span>
                </div>
            </div>
        </div>

        {{-- Plan Features & Limits (Pro+) --}}
        @php
            $isPro = in_array($plan, ['pro', 'enterprise']);
            $isEnterprise = $plan === 'enterprise';
            $planConfig = \App\Services\PlanService::getPlan($plan);
            $limits = $planConfig['limits'] ?? [];
            $features = $planConfig['features'] ?? [];

            // Usage actuelle
            $invoicesUsed = $stats['invoices_this_month'] ?? 0;
            $invoicesLimit = $limits['invoices_per_month'] ?? 10;
            $clientsUsed = $stats['total_clients'] ?? 0;
            $clientsLimit = $limits['clients'] ?? 5;
            $productsUsed = \App\Models\Product::where('tenant_id', auth()->user()->tenant_id)->count();
            $productsLimit = $limits['products'] ?? 20;
        @endphp

        @if ($isPro)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/25">
                                <span class="text-lg">⭐</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Fonctionnalités Plan {{ $currentPlanName }}
                                </h2>
                                <p class="text-xs text-gray-500">Vos fonctionnalités actives et utilisation</p>
                            </div>
                        </div>
                        <a href="{{ route('client.billing') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            Gérer mon plan →
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Usage Limits --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        {{-- Factures ce mois --}}
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600">📄 Factures / mois</span>
                                <span
                                    class="text-xs font-bold {{ $invoicesLimit == -1 ? 'text-green-600' : ($invoicesUsed >= $invoicesLimit ? 'text-red-600' : 'text-indigo-600') }}">
                                    {{ $invoicesUsed }} / {{ $invoicesLimit == -1 ? '∞' : $invoicesLimit }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php $invoicePercent = $invoicesLimit == -1 ? 15 : min(100, ($invoicesUsed / max(1, $invoicesLimit)) * 100); @endphp
                                <div class="h-2 rounded-full transition-all {{ $invoicePercent > 80 ? 'bg-red-500' : 'bg-indigo-500' }}"
                                    style="width: {{ $invoicePercent }}%"></div>
                            </div>
                        </div>

                        {{-- Clients --}}
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600">👥 Clients</span>
                                <span
                                    class="text-xs font-bold {{ $clientsLimit == -1 ? 'text-green-600' : ($clientsUsed >= $clientsLimit ? 'text-red-600' : 'text-indigo-600') }}">
                                    {{ $clientsUsed }} / {{ $clientsLimit == -1 ? '∞' : $clientsLimit }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php $clientPercent = $clientsLimit == -1 ? 10 : min(100, ($clientsUsed / max(1, $clientsLimit)) * 100); @endphp
                                <div class="h-2 rounded-full transition-all {{ $clientPercent > 80 ? 'bg-red-500' : 'bg-indigo-500' }}"
                                    style="width: {{ $clientPercent }}%"></div>
                            </div>
                        </div>

                        {{-- Produits --}}
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600">📦 Produits</span>
                                <span
                                    class="text-xs font-bold {{ $productsLimit == -1 ? 'text-green-600' : ($productsUsed >= $productsLimit ? 'text-red-600' : 'text-indigo-600') }}">
                                    {{ $productsUsed }} / {{ $productsLimit == -1 ? '∞' : $productsLimit }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php $productPercent = $productsLimit == -1 ? 5 : min(100, ($productsUsed / max(1, $productsLimit)) * 100); @endphp
                                <div class="h-2 rounded-full transition-all {{ $productPercent > 80 ? 'bg-red-500' : 'bg-indigo-500' }}"
                                    style="width: {{ $productPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Active Features Grid --}}
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Fonctionnalités actives</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @php
                            $featureLabels = [
                                'basic_invoicing' => [
                                    'label' => 'Facturation',
                                    'icon' => '📄',
                                    'link' => 'client.invoices.index',
                                ],
                                'pdf_export' => ['label' => 'Export PDF', 'icon' => '📥', 'link' => null],
                                'email_sending' => ['label' => 'Envoi email', 'icon' => '📧', 'link' => null],
                                'client_management' => [
                                    'label' => 'Gestion clients',
                                    'icon' => '👥',
                                    'link' => 'client.clients.index',
                                ],
                                'product_catalog' => [
                                    'label' => 'Catalogue produits',
                                    'icon' => '📦',
                                    'link' => 'client.products.index',
                                ],
                                'analytics' => [
                                    'label' => 'Analytiques',
                                    'icon' => '📊',
                                    'link' => 'client.analytics.index',
                                ],
                                'custom_templates' => [
                                    'label' => 'Templates Pro',
                                    'icon' => '🎨',
                                    'link' => 'client.templates.index',
                                ],
                                'two_factor_auth' => [
                                    'label' => 'Sécurité 2FA',
                                    'icon' => '🔐',
                                    'link' => 'client.two-factor.enable',
                                ],
                                'multi_currency' => ['label' => 'Multi-devises', 'icon' => '💱', 'link' => null],
                                'recurring_invoices' => [
                                    'label' => 'Factures récurrentes',
                                    'icon' => '🔄',
                                    'link' => null,
                                ],
                                'payment_reminders' => ['label' => 'Relances auto', 'icon' => '⏰', 'link' => null],
                                'priority_support' => [
                                    'label' => 'Support prioritaire',
                                    'icon' => '🎯',
                                    'link' => null,
                                ],
                                'team_management' => [
                                    'label' => 'Gestion équipe',
                                    'icon' => '👨‍💼',
                                    'link' => 'client.team.index',
                                ],
                                'api_access' => [
                                    'label' => 'Accès API',
                                    'icon' => '🔗',
                                    'link' => 'client.api-keys.index',
                                ],
                                'white_label' => ['label' => 'White Label', 'icon' => '🏷️', 'link' => null],
                            ];
                        @endphp

                        @foreach ($featureLabels as $featureKey => $featureInfo)
                            @php
                                $isActive = $features[$featureKey] ?? false;
                            @endphp
                            @if ($isActive)
                                @if ($featureInfo['link'])
                                    <a href="{{ route($featureInfo['link']) }}"
                                        class="group flex items-center gap-2 p-3 bg-green-50 border border-green-100 rounded-xl hover:bg-green-100 hover:shadow-sm transition-all">
                                        <span class="text-lg">{{ $featureInfo['icon'] }}</span>
                                        <span
                                            class="text-xs font-medium text-green-800 group-hover:text-green-900">{{ $featureInfo['label'] }}</span>
                                        <svg class="w-3 h-3 text-green-500 ml-auto" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                @else
                                    <div
                                        class="flex items-center gap-2 p-3 bg-green-50 border border-green-100 rounded-xl">
                                        <span class="text-lg">{{ $featureInfo['icon'] }}</span>
                                        <span
                                            class="text-xs font-medium text-green-800">{{ $featureInfo['label'] }}</span>
                                        <svg class="w-3 h-3 text-green-500 ml-auto" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            @else
                                <div
                                    class="flex items-center gap-2 p-3 bg-gray-50 border border-gray-100 rounded-xl opacity-50">
                                    <span class="text-lg grayscale">{{ $featureInfo['icon'] }}</span>
                                    <span class="text-xs font-medium text-gray-400">{{ $featureInfo['label'] }}</span>
                                    <svg class="w-3 h-3 text-gray-300 ml-auto" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Upgrade CTA for Pro --}}
                    @if (!$isEnterprise)
                        <div
                            class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">🚀</span>
                                    <div>
                                        <h4 class="text-sm font-bold text-purple-900">Passez à Enterprise</h4>
                                        <p class="text-xs text-purple-600">Débloquez l'accès API, la gestion d'équipe et le
                                            White Label</p>
                                    </div>
                                </div>
                                <a href="{{ route('client.billing') }}"
                                    class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium rounded-xl hover:shadow-lg transition-all">
                                    Voir Enterprise
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            {{-- Starter upgrade banner --}}
            <div
                class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl p-6 text-white overflow-hidden relative">
                <div class="absolute top-0 right-0 -mt-6 -mr-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-1">⭐ Passez au Plan Pro</h3>
                        <p class="text-white/80 text-sm">Débloquez : Clients, Analytiques, Templates Pro, 2FA, Exports CSV,
                            Multi-devises, Relances auto et plus !</p>
                        <p class="text-white/60 text-xs mt-1">À partir de 19 000 XOF/mois</p>
                    </div>
                    <a href="{{ route('client.billing') }}"
                        class="flex-shrink-0 ml-4 px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all shadow-lg">
                        Voir les plans
                    </a>
                </div>
            </div>
        @endif

        {{-- Recent Invoices & Payments --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Invoices --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Factures récentes</h2>
                    <a href="{{ route('client.invoices.index') }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Voir tout &rarr;
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentInvoices as $invoice)
                        <a href="{{ route('client.invoices.show', $invoice) }}"
                            class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $invoice->invoice_number ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $invoice->client->company_name ?? ($invoice->client->name ?? 'Client') }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 ml-3">
                                <p class="text-sm font-bold text-gray-900">
                                    {{ number_format($invoice->total ?? 0, 0, ',', ' ') }} XOF</p>
                                @php
                                    $statusColors = [
                                        'paid' => 'bg-green-100 text-green-700',
                                        'sent' => 'bg-blue-100 text-blue-700',
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'overdue' => 'bg-red-100 text-red-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'viewed' => 'bg-purple-100 text-purple-700',
                                        'cancelled' => 'bg-gray-100 text-gray-500',
                                    ];
                                    $statusLabels = [
                                        'paid' => 'Payée',
                                        'sent' => 'Envoyée',
                                        'draft' => 'Brouillon',
                                        'overdue' => 'En retard',
                                        'pending' => 'En attente',
                                        'viewed' => 'Vue',
                                        'cancelled' => 'Annulée',
                                    ];
                                    $color = $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-700';
                                    $label = $statusLabels[$invoice->status] ?? ucfirst($invoice->status);
                                @endphp
                                <span
                                    class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full {{ $color }}">
                                    {{ $label }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm text-gray-500">Aucune facture pour le moment</p>
                            <a href="{{ route('client.invoices.create') }}"
                                class="mt-3 inline-flex items-center text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Créer votre première facture &rarr;
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Payments --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Paiements récents</h2>
                    <a href="{{ route('client.payments.index') }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Voir tout &rarr;
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentPayments as $payment)
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $payment->invoice->invoice_number ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $payment->created_at ? $payment->created_at->format('d/m/Y') : '' }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 ml-3">
                                <p class="text-sm font-bold text-green-600">
                                    +{{ number_format($payment->amount ?? 0, 0, ',', ' ') }} XOF</p>
                                <span
                                    class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                    {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <p class="text-sm text-gray-500">Aucun paiement reçu</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Upgrade Modal --}}
        <div x-show="showUpgradeModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @click.self="showUpgradeModal = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Débloquez cette fonctionnalité</h3>
                    <p class="text-gray-600 mb-6">
                        La fonctionnalité <strong x-text="currentFeature"></strong> est disponible avec le plan Pro ou
                        Enterprise.
                    </p>
                    <div class="flex gap-3">
                        <button @click="showUpgradeModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition font-medium">
                            Plus tard
                        </button>
                        <a href="{{ route('client.settings.index') }}"
                            class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition font-medium text-center">
                            Voir les plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
