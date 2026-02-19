@extends('layouts.dashboard')

@section('title', 'Facturation & Abonnement')

@section('content')
    <div x-data="billingPage()" class="space-y-8">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Facturation & Abonnement</h1>
                <p class="mt-1 text-sm text-gray-500">Gérez votre plan et suivez votre utilisation</p>
            </div>
        </div>

        <!-- Alerte Trial -->
        @if ($isOnTrial)
            <div
                class="relative overflow-hidden bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-amber-200/30 rounded-full"></div>
                <div class="relative flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-amber-900">Période d'essai en cours</h3>
                        <p class="mt-1 text-sm text-amber-700">
                            Il vous reste <span class="font-bold text-amber-900">{{ $trialDaysRemaining }}
                                jour{{ $trialDaysRemaining > 1 ? 's' : '' }}</span>
                            d'essai gratuit sur le plan <span
                                class="font-semibold">{{ $plans[$currentPlan]['name'] }}</span>.
                            Passez à un abonnement payant pour ne pas perdre l'accès aux fonctionnalités avancées.
                        </p>
                        <div class="mt-3">
                            <div class="w-full bg-amber-200 rounded-full h-2">
                                @php
                                    $trialProgress = max(0, min(100, ((30 - $trialDaysRemaining) / 30) * 100));
                                @endphp
                                <div class="bg-amber-500 h-2 rounded-full transition-all"
                                    style="width: {{ $trialProgress }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-amber-600">{{ round($trialProgress) }}% de la période d'essai
                                écoulée</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($isTrialExpired)
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-red-900">Période d'essai expirée</h3>
                        <p class="mt-1 text-sm text-red-700">
                            Votre période d'essai du plan <span
                                class="font-semibold">{{ $plans[$currentPlan]['name'] }}</span> a expiré.
                            Veuillez souscrire à un abonnement pour continuer à utiliser les fonctionnalités avancées,
                            ou vous serez limité au plan Starter gratuit.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Plan actuel + Utilisation -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Plan actuel -->
            <div class="lg:col-span-1 bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Plan actuel</h2>
                    @if ($currentPlan !== 'starter')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    {{ $currentPlan === 'enterprise' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $plans[$currentPlan]['name'] }}
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                            Gratuit
                        </span>
                    @endif
                </div>

                <div class="text-center py-6 border-t border-gray-100">
                    <div class="text-4xl font-bold text-gray-900">
                        @if ($plans[$currentPlan]['price'] === 0)
                            Gratuit
                        @else
                            {{ number_format($plans[$currentPlan]['price'], 0, ',', ' ') }}
                            <span class="text-lg font-normal text-gray-500">XOF/mois</span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ $plans[$currentPlan]['description'] }}</p>
                </div>

                @if ($currentPlan !== 'starter')
                    <div class="pt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('client.billing.cancel') }}"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir annuler votre abonnement ? Vous perdrez l\'accès aux fonctionnalités avancées.')">
                            @csrf
                            <button type="submit"
                                class="w-full text-center text-sm text-red-600 hover:text-red-700 font-medium transition-colors">
                                Annuler l'abonnement
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Utilisation -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Utilisation du plan</h2>

                <div class="space-y-6">
                    <!-- Factures ce mois -->
                    @php
                        $invoiceLimit = $limits['invoices_per_month'] ?? 0;
                        $invoiceUsage = $usage['invoices_this_month'] ?? 0;
                        $invoicePercent =
                            $invoiceLimit === -1
                                ? 0
                                : ($invoiceLimit > 0
                                    ? min(100, ($invoiceUsage / $invoiceLimit) * 100)
                                    : 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Factures ce mois</span>
                            </div>
                            <span class="text-sm text-gray-500">
                                {{ $invoiceUsage }} / {{ $invoiceLimit === -1 ? '∞' : $invoiceLimit }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all {{ $invoicePercent >= 90 ? 'bg-red-500' : ($invoicePercent >= 70 ? 'bg-amber-500' : 'bg-blue-500') }}"
                                style="width: {{ $invoiceLimit === -1 ? 5 : $invoicePercent }}%"></div>
                        </div>
                    </div>

                    <!-- Clients -->
                    @php
                        $clientLimit = $limits['clients'] ?? 0;
                        $clientUsage = $usage['clients'] ?? 0;
                        $clientPercent =
                            $clientLimit === -1
                                ? 0
                                : ($clientLimit > 0
                                    ? min(100, ($clientUsage / $clientLimit) * 100)
                                    : 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Clients</span>
                            </div>
                            <span class="text-sm text-gray-500">
                                {{ $clientUsage }} / {{ $clientLimit === -1 ? '∞' : $clientLimit }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all {{ $clientPercent >= 90 ? 'bg-red-500' : ($clientPercent >= 70 ? 'bg-amber-500' : 'bg-green-500') }}"
                                style="width: {{ $clientLimit === -1 ? 5 : $clientPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Produits -->
                    @php
                        $productLimit = $limits['products'] ?? 0;
                        $productUsage = $usage['products'] ?? 0;
                        $productPercent =
                            $productLimit === -1
                                ? 0
                                : ($productLimit > 0
                                    ? min(100, ($productUsage / $productLimit) * 100)
                                    : 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Produits</span>
                            </div>
                            <span class="text-sm text-gray-500">
                                {{ $productUsage }} / {{ $productLimit === -1 ? '∞' : $productLimit }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all {{ $productPercent >= 90 ? 'bg-red-500' : ($productPercent >= 70 ? 'bg-amber-500' : 'bg-purple-500') }}"
                                style="width: {{ $productLimit === -1 ? 5 : $productPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes des plans -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Choisissez votre plan</h2>
            <p class="text-sm text-gray-500 mb-8">Sélectionnez le plan qui correspond le mieux à vos besoins</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($plans as $planKey => $plan)
                    <div
                        class="relative bg-white rounded-2xl border-2 transition-all duration-200 hover:shadow-lg
                {{ $planKey === $currentPlan ? 'border-blue-500 shadow-lg shadow-blue-500/10' : 'border-gray-200 hover:border-gray-300' }}
                {{ $planKey === 'pro' && $planKey !== $currentPlan ? 'ring-2 ring-blue-500 ring-offset-2' : '' }}">

                        <!-- Badge recommandé -->
                        @if ($planKey === 'pro' && $planKey !== $currentPlan)
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-blue-600 text-white shadow-lg">
                                    ⭐ Recommandé
                                </span>
                            </div>
                        @endif

                        <!-- Badge plan actuel -->
                        @if ($planKey === $currentPlan)
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-green-600 text-white shadow-lg">
                                    ✓ Plan actuel
                                </span>
                            </div>
                        @endif

                        <div class="p-8">
                            <!-- Nom et prix -->
                            <div class="text-center mb-8">
                                <h3 class="text-xl font-bold text-gray-900">{{ $plan['name'] }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $plan['description'] }}</p>
                                <div class="mt-6">
                                    @if ($plan['price'] === 0)
                                        <span class="text-4xl font-bold text-gray-900">Gratuit</span>
                                    @else
                                        <span
                                            class="text-4xl font-bold text-gray-900">{{ number_format($plan['price'], 0, ',', ' ') }}</span>
                                        <span class="text-gray-500 ml-1">XOF/mois</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Limites -->
                            <div class="space-y-3 mb-8">
                                <div class="flex items-center text-sm">
                                    <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-gray-700">
                                        {{ $plan['limits']['invoices_per_month'] === -1 ? 'Factures illimitées' : $plan['limits']['invoices_per_month'] . ' factures/mois' }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-gray-700">
                                        {{ $plan['limits']['clients'] === -1 ? 'Clients illimités' : $plan['limits']['clients'] . ' clients' }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-5 h-5 text-purple-500 mr-3 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span class="text-gray-700">
                                        {{ $plan['limits']['products'] === -1 ? 'Produits illimités' : $plan['limits']['products'] . ' produits' }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                    </svg>
                                    <span
                                        class="text-gray-700">{{ $plan['limits']['storage_mb'] >= 1000 ? $plan['limits']['storage_mb'] / 1000 . ' Go' : $plan['limits']['storage_mb'] . ' Mo' }}
                                        stockage</span>
                                </div>
                            </div>

                            <!-- Fonctionnalités -->
                            <div class="space-y-3 mb-8 pt-6 border-t border-gray-100">
                                @php
                                    $featureLabels = [
                                        'basic_invoicing' => 'Facturation de base',
                                        'pdf_export' => 'Export PDF',
                                        'email_sending' => 'Envoi par email',
                                        'client_management' => 'Gestion des clients',
                                        'product_catalog' => 'Catalogue produits',
                                        'analytics' => 'Tableau de bord analytique',
                                        'custom_templates' => 'Modèles personnalisés',
                                        'two_factor_auth' => 'Authentification 2FA',
                                        'multi_currency' => 'Multi-devises',
                                        'recurring_invoices' => 'Factures récurrentes',
                                        'payment_reminders' => 'Rappels automatiques',
                                        'team_management' => 'Gestion d\'équipe',
                                        'api_access' => 'Accès API',
                                        'priority_support' => 'Support prioritaire',
                                        'white_label' => 'Marque blanche',
                                    ];
                                @endphp
                                @foreach ($featureLabels as $featureKey => $featureLabel)
                                    <div class="flex items-center text-sm">
                                        @if ($plan['features'][$featureKey] ?? false)
                                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-gray-700">{{ $featureLabel }}</span>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300 mr-3 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-gray-400">{{ $featureLabel }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Moyens de paiement -->
                            <div class="mb-8 pt-4 border-t border-gray-100">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Moyens de
                                    paiement</p>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $gatewayLabels = [
                                            'stripe' => '💳 Stripe',
                                            'paystack' => '🏦 Paystack',
                                            'wave' => '🌊 Wave',
                                            'orange_money' => '🟠 Orange Money',
                                            'flutterwave' => '🦋 Flutterwave',
                                            'mtn_momo' => '📱 MTN MoMo',
                                            'mpesa' => '📲 M-Pesa',
                                            'fedapay' => '💰 FedaPay',
                                            'kkiapay' => '🔒 KkiaPay',
                                            'cinetpay' => '🎬 CinetPay',
                                        ];
                                    @endphp
                                    @foreach ($plan['limits']['payment_gateways'] ?? [] as $gateway)
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs rounded-md bg-gray-100 text-gray-600">
                                            {{ $gatewayLabels[$gateway] ?? ucfirst($gateway) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Bouton d'action -->
                            <div>
                                @if ($planKey === $currentPlan)
                                    <button disabled
                                        class="w-full py-3 px-6 text-sm font-semibold rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed">
                                        Plan actuel
                                    </button>
                                @elseif(\App\Services\PlanService::isUpgrade($currentPlan, $planKey))
                                    <form method="POST" action="{{ route('client.billing.upgrade') }}">
                                        @csrf
                                        <input type="hidden" name="plan" value="{{ $planKey }}">
                                        <button type="submit"
                                            class="w-full py-3 px-6 text-sm font-semibold rounded-xl text-white transition-all duration-200
                                    {{ $planKey === 'pro' ? 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30' : 'bg-purple-600 hover:bg-purple-700 shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/30' }}">
                                            🚀 Passer au {{ $plan['name'] }}
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('client.billing.downgrade') }}"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir rétrograder vers le plan {{ $plan['name'] }} ? Certaines fonctionnalités ne seront plus disponibles.')">
                                        @csrf
                                        <input type="hidden" name="plan" value="{{ $planKey }}">
                                        <button type="submit"
                                            class="w-full py-3 px-6 text-sm font-semibold rounded-xl border-2 border-gray-300 text-gray-700 hover:bg-gray-50 transition-all duration-200">
                                            Rétrograder vers {{ $plan['name'] }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tableau comparatif -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Comparaison détaillée des plans</h2>
                <p class="mt-1 text-sm text-gray-500">Voir toutes les différences entre les plans</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-8 py-4 text-left text-sm font-semibold text-gray-900 w-1/3">Fonctionnalité</th>
                            @foreach ($plans as $planKey => $plan)
                                <th
                                    class="px-6 py-4 text-center text-sm font-semibold {{ $planKey === $currentPlan ? 'text-blue-700 bg-blue-50' : 'text-gray-900' }}">
                                    {{ $plan['name'] }}
                                    @if ($planKey === $currentPlan)
                                        <span class="block text-xs font-normal text-blue-500">Votre plan</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Limites -->
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-8 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Limites</td>
                        </tr>
                        <tr>
                            <td class="px-8 py-3.5 text-sm text-gray-700">Factures / mois</td>
                            @foreach ($plans as $planKey => $plan)
                                <td
                                    class="px-6 py-3.5 text-sm text-center {{ $planKey === $currentPlan ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-900' }}">
                                    {{ $plan['limits']['invoices_per_month'] === -1 ? 'Illimité' : $plan['limits']['invoices_per_month'] }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-8 py-3.5 text-sm text-gray-700">Clients</td>
                            @foreach ($plans as $planKey => $plan)
                                <td
                                    class="px-6 py-3.5 text-sm text-center {{ $planKey === $currentPlan ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-900' }}">
                                    {{ $plan['limits']['clients'] === -1 ? 'Illimité' : $plan['limits']['clients'] }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-8 py-3.5 text-sm text-gray-700">Produits</td>
                            @foreach ($plans as $planKey => $plan)
                                <td
                                    class="px-6 py-3.5 text-sm text-center {{ $planKey === $currentPlan ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-900' }}">
                                    {{ $plan['limits']['products'] === -1 ? 'Illimité' : $plan['limits']['products'] }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-8 py-3.5 text-sm text-gray-700">Membres d'équipe</td>
                            @foreach ($plans as $planKey => $plan)
                                <td
                                    class="px-6 py-3.5 text-sm text-center {{ $planKey === $currentPlan ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-900' }}">
                                    {{ $plan['limits']['team_members'] }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-8 py-3.5 text-sm text-gray-700">Stockage</td>
                            @foreach ($plans as $planKey => $plan)
                                <td
                                    class="px-6 py-3.5 text-sm text-center {{ $planKey === $currentPlan ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-900' }}">
                                    {{ $plan['limits']['storage_mb'] >= 1000 ? $plan['limits']['storage_mb'] / 1000 . ' Go' : $plan['limits']['storage_mb'] . ' Mo' }}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Fonctionnalités -->
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-8 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Fonctionnalités</td>
                        </tr>
                        @php
                            $comparisonFeatures = [
                                'basic_invoicing' => 'Facturation de base',
                                'pdf_export' => 'Export PDF',
                                'email_sending' => 'Envoi par email',
                                'client_management' => 'Gestion des clients',
                                'product_catalog' => 'Catalogue produits',
                                'analytics' => 'Tableau de bord analytique',
                                'custom_templates' => 'Modèles personnalisés',
                                'two_factor_auth' => 'Authentification 2FA',
                                'multi_currency' => 'Multi-devises (XOF, EUR, USD...)',
                                'recurring_invoices' => 'Factures récurrentes',
                                'payment_reminders' => 'Rappels de paiement automatiques',
                                'team_management' => 'Gestion d\'équipe',
                                'api_access' => 'Accès API REST',
                                'priority_support' => 'Support prioritaire',
                                'white_label' => 'Marque blanche',
                            ];
                        @endphp
                        @foreach ($comparisonFeatures as $featureKey => $featureLabel)
                            <tr>
                                <td class="px-8 py-3.5 text-sm text-gray-700">{{ $featureLabel }}</td>
                                @foreach ($plans as $planKey => $plan)
                                    <td
                                        class="px-6 py-3.5 text-center {{ $planKey === $currentPlan ? 'bg-blue-50' : '' }}">
                                        @if ($plan['features'][$featureKey] ?? false)
                                            <svg class="w-5 h-5 text-green-500 mx-auto" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300 mx-auto" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <!-- Moyens de paiement -->
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-8 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Moyens de paiement acceptés</td>
                        </tr>
                        <tr>
                            <td class="px-8 py-3.5 text-sm text-gray-700">Passerelles de paiement</td>
                            @foreach ($plans as $planKey => $plan)
                                <td
                                    class="px-6 py-3.5 text-sm text-center {{ $planKey === $currentPlan ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-900' }}">
                                    {{ count($plan['limits']['payment_gateways']) }}
                                    passerelle{{ count($plan['limits']['payment_gateways']) > 1 ? 's' : '' }}
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Questions fréquentes</h2>
            <div class="space-y-4">
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="text-sm font-medium text-gray-900">Puis-je changer de plan à tout moment ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="px-4 pb-3">
                        <p class="text-sm text-gray-600">Oui ! Vous pouvez passer à un plan supérieur ou inférieur à tout
                            moment. Les changements prennent effet immédiatement pour les upgrades. Pour les downgrades, le
                            changement prend effet à la fin de la période de facturation en cours.</p>
                    </div>
                </div>
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="text-sm font-medium text-gray-900">Que se passe-t-il si je dépasse les limites de mon
                            plan ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="px-4 pb-3">
                        <p class="text-sm text-gray-600">Vous recevrez une notification lorsque vous approchez de vos
                            limites. Une fois atteintes, vous ne pourrez plus créer de nouvelles ressources (factures,
                            clients, produits) tant que vous n'aurez pas passé à un plan supérieur ou attendu le prochain
                            cycle de facturation.</p>
                    </div>
                </div>
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="text-sm font-medium text-gray-900">La période d'essai est-elle vraiment gratuite
                            ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="px-4 pb-3">
                        <p class="text-sm text-gray-600">Oui, la période d'essai de 30 jours est entièrement gratuite et ne
                            nécessite aucune carte bancaire. Vous pouvez tester toutes les fonctionnalités du plan choisi
                            pendant cette période.</p>
                    </div>
                </div>
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="text-sm font-medium text-gray-900">Quels moyens de paiement acceptez-vous ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="px-4 pb-3">
                        <p class="text-sm text-gray-600">Nous acceptons les cartes bancaires (Visa, Mastercard) via Stripe,
                            le mobile money (Wave, Orange Money, MTN MoMo, M-Pesa), et les solutions de paiement africaines
                            (Paystack, Flutterwave, FedaPay, KkiaPay, CinetPay). Les moyens disponibles dépendent de votre
                            plan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function billingPage() {
            return {
                // Données réactives si nécessaire pour des interactions futures
            }
        }
    </script>
@endsection
