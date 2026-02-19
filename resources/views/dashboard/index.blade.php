@extends('layouts.dashboard')@extends('layouts.dashboard')



@section('title', 'Tableau de bord')@section('title', 'Tableau de bord')



@section('content')@section('content')

<div x-data="dashboard()" x-init="init()" class="space-y-8"><div x-data="{ showUpgradeModal: false, currentFeature: '' }" class="space-y-6">



    @php    @php

        $user = auth()->user();        $user = auth()->user();

        $plan = $user->plan ?? 'starter';        $plan = $user->plan ?? 'starter';

        $trialEndsAt = $user->trial_ends_at ? \Carbon\Carbon::parse($user->trial_ends_at) : null;        $trialEndsAt = $user->trial_ends_at ? \Carbon\Carbon::parse($user->trial_ends_at) : null;

        $isOnTrial = $trialEndsAt && $trialEndsAt->isFuture();        $isOnTrial = $trialEndsAt && $trialEndsAt->isFuture();

        $daysLeft = $isOnTrial ? now()->diffInDays($trialEndsAt) : 0;        $daysLeft = $isOnTrial ? now()->diffInDays($trialEndsAt) : 0;

        $planNames = ['starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'];        $planNames = ['starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'];

        $planEmojis = ['starter' => '🚀', 'pro' => '⚡', 'enterprise' => '👑'];        $planColors = ['starter' => 'blue', 'pro' => 'purple', 'enterprise' => 'amber'];

        $currentPlanName = $planNames[$plan] ?? 'Starter';        $currentPlanName = $planNames[$plan] ?? 'Starter';

        $planEmoji = $planEmojis[$plan] ?? '🚀';        $planColor = $planColors[$plan] ?? 'blue';

            @endphp

        // Calculs pour les graphiques

        $totalInvoices = $stats['invoices_count'] ?? 0;    @if(session('success'))

        $paidCount = $stats['paid_count'] ?? 0;    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">

        $pendingCount = $stats['pending_count'] ?? 0;        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

        $overdueCount = $stats['overdue_count'] ?? 0;            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>

        $paidPercentage = $totalInvoices > 0 ? round(($paidCount / $totalInvoices) * 100) : 0;        </svg>

    @endphp        <p class="text-green-800 font-medium flex-1">{{ session('success') }}</p>

    </div>

    <!-- En-tête avec salutation personnalisée -->    @endif

    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl p-8 text-white">

        <!-- Formes décoratives -->    @if(session('error'))

        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">

        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>        <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

        <div class="absolute top-1/2 right-1/4 w-20 h-20 bg-yellow-400/20 rounded-full blur-lg"></div>            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>

                </svg>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">        <p class="text-red-800 font-medium flex-1">{{ session('error') }}</p>

            <div>    </div>

                <div class="flex items-center gap-3 mb-2">    @endif

                    <span class="text-4xl">{{ $planEmoji }}</span>

                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">    @if($isOnTrial)

                        Plan {{ $currentPlanName }}    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg px-6 py-4">

                    </span>        <div class="flex flex-col sm:flex-row items-center justify-between">

                </div>            <div class="flex items-center space-x-3 text-white mb-3 sm:mb-0">

                <h1 class="text-3xl lg:text-4xl font-bold mb-2">                <div class="p-2 bg-white/20 rounded-lg">

                    Bonjour, {{ explode(' ', $user->name)[0] }} ! 👋                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                </h1>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>

                <p class="text-white/80 text-lg">                    </svg>

                    @php                </div>

                        $hour = now()->hour;                <div>

                        if ($hour < 12) {                    <p class="font-semibold">Période d'essai {{ $currentPlanName }}</p>

                            echo "Belle matinée pour gérer vos factures !";                    <p class="text-sm text-blue-100">{{ $daysLeft }} jours restants</p>

                        } elseif ($hour < 18) {                </div>

                            echo "Continuez sur cette lancée !";            </div>

                        } else {            <a href="{{ route('client.settings.index') }}" class="px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">

                            echo "Encore quelques factures avant la fin de journée ?";                Mettre à niveau

                        }            </a>

                    @endphp        </div>

                </p>    </div>

                    @endif

                @if($isOnTrial)

                <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl">    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">        <div>

                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>            <h1 class="text-2xl font-bold text-gray-900">Bienvenue, {{ $user->name }} 👋</h1>

                    </svg>            <p class="text-gray-600 mt-1">Voici un aperçu de votre activité</p>

                    <span class="font-medium">{{ $daysLeft }} jours d'essai restants</span>        </div>

                </div>        <div class="flex items-center space-x-3">

                @endif            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">

            </div>                Plan {{ $currentPlanName }}

                        </span>

            <div class="flex flex-col sm:flex-row gap-3">            <a href="{{ route('client.invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">

                <a href="{{ route('client.invoices.create') }}"                 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                   class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 group">                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>

                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">                </svg>

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>                Nouvelle facture

                    </svg>            </a>

                    Nouvelle facture        </div>

                </a>    </div>

                <a href="{{ route('client.invoices.index') }}" 

                   class="inline-flex items-center justify-center px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-medium rounded-xl hover:bg-white/30 transition-all duration-300">    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>            <div class="flex items-center justify-between">

                    </svg>                <div>

                    Mes factures                    <p class="text-sm font-medium text-gray-500">Total Factures</p>

                </a>                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['invoices_count'] ?? 0 }}</p>

            </div>                </div>

        </div>                <div class="p-3 bg-blue-100 rounded-xl">

    </div>                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>

    <!-- Statistiques principales avec animations -->                    </svg>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">                </div>

        <!-- Total Factures -->            </div>

        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer"        </div>

             onclick="window.location.href='{{ route('client.invoices.index') }}'">

            <div class="flex items-center justify-between mb-4">        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">

                <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">            <div class="flex items-center justify-between">

                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">                <div>

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>                    <p class="text-sm font-medium text-gray-500">Montant Total</p>

                    </svg>                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }}</p>

                </div>                    <p class="text-sm text-gray-500">XOF</p>

                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Total</span>                </div>

            </div>                <div class="p-3 bg-green-100 rounded-xl">

            <p class="text-3xl lg:text-4xl font-black text-gray-900">{{ $totalInvoices }}</p>                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <p class="text-sm text-gray-500 mt-1">Factures créées</p>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>

        </div>                    </svg>

                </div>

        <!-- Chiffre d'affaires -->            </div>

        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">        </div>

            <div class="flex items-center justify-between mb-4">

                <div class="p-3 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">

                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">            <div class="flex items-center justify-between">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>                <div>

                    </svg>                    <p class="text-sm font-medium text-gray-500">Factures Payées</p>

                </div>                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['paid_count'] ?? 0 }}</p>

                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">Revenus</span>                </div>

            </div>                <div class="p-3 bg-green-100 rounded-xl">

            <p class="text-2xl lg:text-3xl font-black text-gray-900">{{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }}</p>                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <p class="text-sm text-gray-500 mt-1">XOF générés</p>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>

        </div>                    </svg>

                </div>

        <!-- Factures payées -->            </div>

        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">        </div>

            <div class="flex items-center justify-between mb-4">

                <div class="p-3 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">

                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">            <div class="flex items-center justify-between">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>                <div>

                    </svg>                    <p class="text-sm font-medium text-gray-500">En Attente</p>

                </div>                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['pending_count'] ?? 0 }}</p>

                <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Payées</span>                </div>

            </div>                <div class="p-3 bg-amber-100 rounded-xl">

            <div class="flex items-end gap-2">                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <p class="text-3xl lg:text-4xl font-black text-gray-900">{{ $paidCount }}</p>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>

                <span class="text-lg font-bold text-green-500 mb-1">{{ $paidPercentage }}%</span>                    </svg>

            </div>                </div>

            <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">            </div>

                <div class="h-full bg-gradient-to-r from-violet-500 to-purple-600 rounded-full transition-all duration-1000"         </div>

                     style="width: {{ $paidPercentage }}%"></div>    </div>

            </div>

        </div>    <div class="grid lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <!-- En attente -->            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h2>

        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">            <div class="space-y-3">

            <div class="flex items-center justify-between mb-4">                <a href="{{ route('client.invoices.create') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition group">

                <div class="p-3 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">                    <div class="p-2 bg-blue-600 rounded-lg text-white group-hover:bg-blue-700">

                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>

                    </svg>                        </svg>

                </div>                    </div>

                @if($overdueCount > 0)                    <span class="ml-3 font-medium text-gray-900">Créer une facture</span>

                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full animate-pulse">{{ $overdueCount }} en retard!</span>                </a>

                @else

                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">En cours</span>                @if(in_array($plan, ['pro', 'enterprise']))

                @endif                <a href="{{ route('client.clients.create') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition group">

            </div>                    <div class="p-2 bg-green-600 rounded-lg text-white group-hover:bg-green-700">

            <p class="text-3xl lg:text-4xl font-black text-gray-900">{{ $pendingCount }}</p>                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <p class="text-sm text-gray-500 mt-1">En attente de paiement</p>                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>

        </div>                        </svg>

    </div>                    </div>

                    <span class="ml-3 font-medium text-gray-900">Ajouter un client</span>

    <!-- Section principale : Actions + Activité -->                </a>

    <div class="grid lg:grid-cols-3 gap-6">                @else

                        <div class="flex items-center p-3 bg-gray-50 rounded-lg opacity-60">

        <!-- Actions rapides -->                    <div class="p-2 bg-gray-400 rounded-lg text-white">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>

                <span class="text-2xl">⚡</span>                        </svg>

                Actions rapides                    </div>

            </h2>                    <div class="ml-3">

            <div class="space-y-3">                        <span class="font-medium text-gray-500">Ajouter un client</span>

                <!-- Créer facture -->                        <p class="text-xs text-gray-400">Plan Pro requis</p>

                <a href="{{ route('client.invoices.create') }}"                     </div>

                   class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 group border border-blue-100">                </div>

                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">                @endif

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>                @if(in_array($plan, ['pro', 'enterprise']))

                        </svg>                <a href="{{ route('client.analytics.index') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition group">

                    </div>                    <div class="p-2 bg-purple-600 rounded-lg text-white group-hover:bg-purple-700">

                    <div class="ml-4 flex-1">                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <span class="font-semibold text-gray-900">Créer une facture</span>                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>

                        <p class="text-xs text-gray-500">Nouvelle facture en 2 min</p>                        </svg>

                    </div>                    </div>

                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    <span class="ml-3 font-medium text-gray-900">Voir les statistiques</span>

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>                </a>

                    </svg>                @else

                </a>                <div class="flex items-center p-3 bg-gray-50 rounded-lg opacity-60">

                    <div class="p-2 bg-gray-400 rounded-lg text-white">

                <!-- Ajouter client -->                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                @if(in_array($plan, ['pro', 'enterprise']))                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>

                <a href="{{ route('client.clients.create') }}"                         </svg>

                   class="flex items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl hover:from-green-100 hover:to-emerald-100 transition-all duration-300 group border border-green-100">                    </div>

                    <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl text-white shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform">                    <div class="ml-3">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">                        <span class="font-medium text-gray-500">Statistiques</span>

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>                        <p class="text-xs text-gray-400">Plan Pro requis</p>

                        </svg>                    </div>

                    </div>                </div>

                    <div class="ml-4 flex-1">                @endif

                        <span class="font-semibold text-gray-900">Ajouter un client</span>            </div>

                        <p class="text-xs text-gray-500">Gérez vos contacts</p>        </div>

                    </div>

                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>            <div class="p-6 border-b border-gray-200 flex justify-between items-center">

                    </svg>                <h2 class="text-lg font-semibold text-gray-900">Dernières factures</h2>

                </a>                <a href="{{ route('client.invoices.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">

                @else                    Voir tout

                <div class="relative flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 opacity-75">                </a>

                    <div class="p-3 bg-gray-300 rounded-xl text-white">            </div>

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">            <div class="divide-y divide-gray-200">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>                @forelse($recentInvoices ?? [] as $invoice)

                        </svg>                <div class="p-4 hover:bg-gray-50 transition">

                    </div>                    <div class="flex items-center justify-between">

                    <div class="ml-4 flex-1">                        <div class="flex items-center space-x-4">

                        <span class="font-semibold text-gray-500">Gestion clients</span>                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">

                        <p class="text-xs text-gray-400">🔒 Plan Pro requis</p>                                <span class="text-blue-600 font-semibold text-sm">{{ substr($invoice->client->name ?? 'C', 0, 2) }}</span>

                    </div>                            </div>

                </div>                            <div>

                @endif                                <p class="font-medium text-gray-900">{{ $invoice->number }}</p>

                                <p class="text-sm text-gray-500">{{ $invoice->client->name ?? 'Client' }}</p>

                <!-- Statistiques -->                            </div>

                @if(in_array($plan, ['pro', 'enterprise']))                        </div>

                <a href="{{ route('client.analytics.index') }}"                         <div class="text-right">

                   class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl hover:from-purple-100 hover:to-pink-100 transition-all duration-300 group border border-purple-100">                            <p class="font-semibold text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} XOF</p>

                    <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl text-white shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">                                @if($invoice->status === 'paid') bg-green-100 text-green-800

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>                                @elseif($invoice->status === 'sent') bg-blue-100 text-blue-800

                        </svg>                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-800

                    </div>                                @else bg-gray-100 text-gray-800 @endif">

                    <div class="ml-4 flex-1">                                @if($invoice->status === 'paid') Payée

                        <span class="font-semibold text-gray-900">Analytiques</span>                                @elseif($invoice->status === 'sent') Envoyée

                        <p class="text-xs text-gray-500">Tableaux de bord avancés</p>                                @elseif($invoice->status === 'overdue') En retard

                    </div>                                @else Brouillon @endif

                    <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">                            </span>

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>                        </div>

                    </svg>                    </div>

                </a>                </div>

                @else                @empty

                <div class="relative flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 opacity-75">                <div class="p-8 text-center">

                    <div class="p-3 bg-gray-300 rounded-xl text-white">                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>                    </svg>

                        </svg>                    <p class="text-gray-500 mb-4">Aucune facture pour le moment</p>

                    </div>                    <a href="{{ route('client.invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

                    <div class="ml-4 flex-1">                        Créer votre première facture

                        <span class="font-semibold text-gray-500">Analytiques</span>                    </a>

                        <p class="text-xs text-gray-400">🔒 Plan Pro requis</p>                </div>

                    </div>                @endforelse

                </div>            </div>

                @endif        </div>

    </div>

                <!-- Paramètres -->

                <a href="{{ route('client.settings.index') }}"     @if($plan === 'starter')

                   class="flex items-center p-4 bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl hover:from-gray-100 hover:to-slate-100 transition-all duration-300 group border border-gray-200">    <div class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl shadow-xl p-8 text-white">

                    <div class="p-3 bg-gradient-to-br from-gray-600 to-slate-700 rounded-xl text-white shadow-lg shadow-gray-500/30 group-hover:scale-110 transition-transform">        <div class="flex flex-col md:flex-row items-center justify-between gap-6">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">            <div>

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>                <h3 class="text-2xl font-bold mb-2">Passez au plan Pro</h3>

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>                <p class="text-purple-200 mb-4">Débloquez toutes les fonctionnalités</p>

                        </svg>                <ul class="space-y-2">

                    </div>                    <li class="flex items-center">

                    <div class="ml-4 flex-1">                        <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">

                        <span class="font-semibold text-gray-900">Paramètres</span>                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>

                        <p class="text-xs text-gray-500">Entreprise & paiements</p>                        </svg>

                    </div>                        Factures illimitées

                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    </li>

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>                    <li class="flex items-center">

                    </svg>                        <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">

                </a>                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>

            </div>                        </svg>

        </div>                        Gestion des clients

                    </li>

        <!-- Dernières factures -->                    <li class="flex items-center">

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">                        <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">

            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>

                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">                        </svg>

                    <span class="text-2xl">📋</span>                        Statistiques avancées

                    Dernières factures                    </li>

                </h2>                </ul>

                <a href="{{ route('client.invoices.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-semibold group">            </div>

                    Voir tout            <div class="text-center">

                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">                <p class="text-4xl font-black mb-1">19 000 <span class="text-xl">XOF</span></p>

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>                <p class="text-purple-200 mb-4">/mois</p>

                    </svg>                <a href="{{ route('client.settings.index') }}" class="inline-block px-8 py-3 bg-white text-purple-700 font-bold rounded-xl hover:bg-purple-50 transition shadow-lg">

                </a>                    Mettre à niveau

            </div>                </a>

                        </div>

            <div class="divide-y divide-gray-100">        </div>

                @forelse($recentInvoices ?? [] as $invoice)    </div>

                <a href="{{ route('client.invoices.show', $invoice) }}" class="block p-4 hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-white transition-all duration-300 group">    @endif

                    <div class="flex items-center justify-between"></div>

                        <div class="flex items-center space-x-4">@endsection

                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                                    <span class="text-white font-bold text-sm">{{ strtoupper(substr($invoice->client->name ?? 'C', 0, 2)) }}</span>
                                </div>
                                @if($invoice->status === 'paid')
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $invoice->number }}</p>
                                <p class="text-sm text-gray-500">{{ $invoice->client->name ?? 'Client' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} <span class="text-xs text-gray-400">XOF</span></p>
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full 
                                @if($invoice->status === 'paid') bg-green-100 text-green-700
                                @elseif($invoice->status === 'sent') bg-blue-100 text-blue-700
                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-600 @endif">
                                @if($invoice->status === 'paid') ✓ Payée
                                @elseif($invoice->status === 'sent') 📤 Envoyée
                                @elseif($invoice->status === 'overdue') ⚠️ En retard
                                @else 📝 Brouillon @endif
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Aucune facture pour le moment</h3>
                    <p class="text-gray-500 mb-6">Créez votre première facture et commencez à suivre vos revenus</p>
                    <a href="{{ route('client.invoices.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer ma première facture
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bannière upgrade pour Starter -->
    @if($plan === 'starter')
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 rounded-3xl p-8 text-white">
        <!-- Effets décoratifs -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxNCAwIDYtMi42ODYgNi02cy0yLjY4Ni02LTYtNi02IDIuNjg2LTYgNiAyLjY4NiA2IDYgNnoiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjEpIiBzdHJva2Utd2lkdGg9IjIiLz48L2c+PC9zdmc+')] opacity-30"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-yellow-400/20 to-pink-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-blue-500/20 to-purple-500/20 rounded-full blur-2xl"></div>
        
        <div class="relative flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full mb-4">
                    <span class="text-xl">🚀</span>
                    <span class="text-sm font-semibold">Offre limitée</span>
                </div>
                <h3 class="text-3xl lg:text-4xl font-black mb-4">
                    Passez au niveau supérieur avec <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-pink-400">Pro</span>
                </h3>
                <p class="text-lg text-white/70 mb-6">Débloquez des fonctionnalités puissantes pour développer votre activité</p>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex items-center gap-3 p-3 bg-white/10 backdrop-blur-sm rounded-xl">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Factures illimitées</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white/10 backdrop-blur-sm rounded-xl">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Gestion clients</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white/10 backdrop-blur-sm rounded-xl">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Statistiques avancées</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white/10 backdrop-blur-sm rounded-xl">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Templates Pro</span>
                    </div>
                </div>
            </div>
            
            <div class="text-center lg:text-right">
                <div class="inline-block p-6 bg-white/10 backdrop-blur-sm rounded-2xl mb-4">
                    <p class="text-sm text-white/60 line-through mb-1">25 000 XOF</p>
                    <p class="text-5xl font-black">19 000</p>
                    <p class="text-white/80">XOF <span class="text-sm">/mois</span></p>
                </div>
                <div>
                    <a href="{{ route('client.settings.index') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-pink-500 text-gray-900 font-black rounded-xl hover:from-yellow-300 hover:to-pink-400 transition-all duration-300 shadow-2xl hover:shadow-yellow-500/25 hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Passer à Pro maintenant
                    </a>
                    <p class="text-xs text-white/50 mt-3">Annulation possible à tout moment</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Section conseils/tips -->
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                <span class="text-2xl">💡</span>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Astuce du jour</h3>
            <p class="text-sm text-gray-600">Ajoutez votre numéro Wave ou Orange Money dans les paramètres pour permettre à vos clients de vous payer plus facilement via mobile.</p>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                <span class="text-2xl">📱</span>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Paiement mobile</h3>
            <p class="text-sm text-gray-600">Vos factures peuvent inclure un QR code pour un paiement instantané via Wave, Orange Money, MTN MoMo ou Moov Money.</p>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                <span class="text-2xl">📧</span>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Envoi automatique</h3>
            <p class="text-sm text-gray-600">Envoyez vos factures directement par email à vos clients avec un lien de paiement sécurisé inclus.</p>
        </div>
    </div>
</div>

<script>
function dashboard() {
    return {
        init() {
            // Initialisation des animations
        }
    }
}
</script>

<style>
/* Animations personnalisées */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #6366f1, #8b5cf6);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #4f46e5, #7c3aed);
}
</style>
@endsection
