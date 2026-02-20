{{--
    Client Interface Layout
    Interface destinée aux clients finaux pour créer et gérer leurs factures
    /admin (Filament) = Back-office administrateur
    /client = Front-office client
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Invoice SaaS') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen" x-data="{ sidebarOpen: false }">

        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-cloak x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-gray-600/75 lg:hidden"></div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <a href="{{ route('client.index') }}" class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-xl font-bold text-gray-900">InvoicePro</span>
                </a>
                <!-- Close button (mobile) -->
                <button @click="sidebarOpen = false" class="lg:hidden p-1 text-gray-400 hover:text-gray-600 rounded-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <a href="{{ route('client.index') }}"
                    class="{{ request()->routeIs('client.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Tableau de bord
                </a>

                <a href="{{ route('client.invoices.index') }}"
                    class="{{ request()->routeIs('client.invoices*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Mes factures
                </a>

                <a href="{{ route('client.payments.index') }}"
                    class="{{ request()->routeIs('client.payments*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Paiements
                </a>

                @php
                    $userPlan = auth()->user()->plan ?? 'starter';
                    $tenant = auth()->user()->tenant;
                    $isOnTrial = $tenant && $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
                    $isPro = in_array($userPlan, ['pro', 'enterprise']);
                    $isEnterprise = $userPlan === 'enterprise';
                @endphp

                <!-- Clients (Pro+) -->
                @if ($isPro)
                    <a href="{{ route('client.clients.index') }}"
                        class="{{ request()->routeIs('client.clients*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Clients
                        <span class="ml-auto px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded-full">Pro</span>
                    </a>
                @endif

                <!-- Produits & Services -->
                <a href="{{ route('client.products.index') }}"
                    class="{{ request()->routeIs('client.products*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Produits & Services
                </a>

                <!-- Analytiques (Pro+) -->
                @if ($isPro)
                    <a href="{{ route('client.analytics.index') }}"
                        class="{{ request()->routeIs('client.analytics*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Analytiques
                        <span class="ml-auto px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded-full">Pro</span>
                    </a>
                @endif

                <!-- Exports CSV (Pro+) -->
                @if ($isPro)
                    <div x-data="{ openExports: false }" class="relative">
                        <button @click="openExports = !openExports" type="button"
                            class="{{ request()->routeIs('client.exports*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center w-full px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exports CSV
                            <span class="ml-auto flex items-center gap-1">
                                <span class="px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded-full">Pro</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform"
                                    :class="{ 'rotate-180': openExports }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </button>
                        <div x-show="openExports" x-cloak x-transition class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('client.exports.invoices') }}"
                                class="flex items-center px-4 py-2 text-xs font-medium text-gray-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                📄 Factures
                            </a>
                            <a href="{{ route('client.exports.clients') }}"
                                class="flex items-center px-4 py-2 text-xs font-medium text-gray-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                👥 Clients
                            </a>
                            <a href="{{ route('client.exports.products') }}"
                                class="flex items-center px-4 py-2 text-xs font-medium text-gray-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                📦 Produits
                            </a>
                            <a href="{{ route('client.exports.payments') }}"
                                class="flex items-center px-4 py-2 text-xs font-medium text-gray-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                💰 Paiements
                            </a>
                        </div>
                    </div>
                @endif

                <a href="{{ route('client.profile.edit') }}"
                    class="{{ request()->routeIs('client.profile*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil
                </a>

                <!-- Facturation / Plans -->
                <a href="{{ route('client.billing') }}"
                    class="{{ request()->routeIs('client.billing*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Facturation
                    @if ($isOnTrial ?? false)
                        <span class="ml-auto px-2 py-0.5 text-xs bg-amber-100 text-amber-700 rounded-full">Trial</span>
                    @endif
                </a>

                <!-- Templates de factures -->
                <a href="{{ route('client.templates.index') }}"
                    class="{{ request()->routeIs('client.templates*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    Modèles
                    @if ($isPro)
                        <span class="ml-auto px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded-full">Pro</span>
                    @endif
                </a>

                <!-- Sécurité 2FA (Pro+) -->
                @if ($isPro)
                    <a href="{{ route('client.two-factor.enable') }}"
                        class="{{ request()->routeIs('client.two-factor*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Sécurité 2FA
                        @if (auth()->user()->two_factor_secret)
                            <span
                                class="ml-auto px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Activé</span>
                        @else
                            <span
                                class="ml-auto px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded-full">Pro</span>
                        @endif
                    </a>
                @endif

                <!-- Équipe (Enterprise only) -->
                @if ($isEnterprise)
                    <a href="{{ route('client.team.index') }}"
                        class="{{ request()->routeIs('client.team*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Équipe
                        <span
                            class="ml-auto px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full">Enterprise</span>
                    </a>
                @endif

                <!-- API Keys (Enterprise only) -->
                @if ($isEnterprise)
                    <a href="{{ route('client.api-keys.index') }}"
                        class="{{ request()->routeIs('client.api-keys*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Clés API
                        <span
                            class="ml-auto px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full">Enterprise</span>
                    </a>
                @endif

                <div class="pt-6 mt-6 border-t border-gray-200">
                    <a href="{{ route('client.settings.index') }}"
                        class="{{ request()->routeIs('client.settings*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Paramètres
                    </a>
                </div>

                <!-- Plan Badge -->
                @if ($isPro)
                    <div
                        class="mx-2 mt-4 p-3 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">⭐</span>
                            <span class="text-sm font-bold text-indigo-700">Plan {{ ucfirst($userPlan) }}</span>
                        </div>
                        <p class="text-xs text-indigo-600/70">Accès aux fonctionnalités avancées</p>
                        @if (!$isEnterprise)
                            <a href="{{ route('client.billing') }}"
                                class="mt-2 flex items-center text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Passer à Enterprise →
                            </a>
                        @endif
                    </div>
                @else
                    <div
                        class="mx-2 mt-4 p-3 bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl border border-gray-200">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">🆓</span>
                            <span class="text-sm font-bold text-gray-700">Plan Starter</span>
                        </div>
                        <p class="text-xs text-gray-500">Fonctionnalités limitées</p>
                        <a href="{{ route('client.billing') }}"
                            class="mt-2 flex items-center text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            Débloquer le plan Pro →
                        </a>
                    </div>
                @endif
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                            <span
                                class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Header -->
            <header class="sticky top-0 z-40 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center space-x-4 ml-auto">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="ml-3 text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="ml-3 text-sm text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
