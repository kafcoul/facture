@extends('layouts.dashboard')

@section('title', 'Mon Profil')

@section('content')
    @php
        $user = auth()->user();
        $plan = $user->plan ?? 'starter';
        $planConfig = \App\Services\PlanService::getPlan($plan);
        $planNames = ['starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'];
        $planColors = [
            'starter' => [
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-700',
                'icon' => '🆓',
                'gradient' => 'from-gray-400 to-slate-500',
            ],
            'pro' => [
                'bg' => 'bg-indigo-100',
                'text' => 'text-indigo-700',
                'icon' => '⭐',
                'gradient' => 'from-indigo-500 to-purple-600',
            ],
            'enterprise' => [
                'bg' => 'bg-amber-100',
                'text' => 'text-amber-700',
                'icon' => '🏆',
                'gradient' => 'from-amber-500 to-orange-600',
            ],
        ];
        $currentPlan = $planColors[$plan] ?? $planColors['starter'];
        $tenant = $user->tenant;
        $initials = collect(explode(' ', $user->name))
            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->take(2)
            ->join('');
        $memberSince = $user->created_at ? $user->created_at->translatedFormat('d F Y') : 'N/A';
        $lastLogin = $user->last_login_at
            ? \Carbon\Carbon::parse($user->last_login_at)->translatedFormat('d M Y à H:i')
            : 'Jamais';
        $has2FA = !empty($user->two_factor_secret);
        $invoiceCount = \App\Models\Invoice::where('tenant_id', $user->tenant_id)->count();
        $clientCount = \App\Models\Client::where('tenant_id', $user->tenant_id)->count();
    @endphp

    <div x-data="{ activeTab: 'personal', showPasswordFields: false }" class="max-w-5xl mx-auto space-y-6">

        {{-- ═══════════════════════════════════════════════
         HEADER CARD WITH AVATAR + STATS
    ═══════════════════════════════════════════════ --}}
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100">
            {{-- Background gradient --}}
            <div class="h-32 bg-gradient-to-r {{ $currentPlan['gradient'] }} relative">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-6 -ml-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            </div>

            <div class="relative px-6 pb-6">
                {{-- Avatar + User Info --}}
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12">
                    <div class="relative">
                        <div
                            class="w-24 h-24 bg-gradient-to-br {{ $currentPlan['gradient'] }} rounded-2xl flex items-center justify-center ring-4 ring-white shadow-lg">
                            <span class="text-3xl font-bold text-white">{{ $initials }}</span>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 w-7 h-7 {{ $has2FA ? 'bg-green-500' : 'bg-gray-400' }} rounded-full flex items-center justify-center ring-2 ring-white">
                            @if ($has2FA)
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 sm:pb-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 sm:pb-1">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $currentPlan['bg'] }} {{ $currentPlan['text'] }} rounded-full text-sm font-semibold">
                            <span>{{ $currentPlan['icon'] }}</span> Plan {{ $planNames[$plan] ?? 'Starter' }}
                        </span>
                        @if ($has2FA)
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 rounded-full text-xs font-medium">🔐
                                2FA Activé</span>
                        @endif
                        @if ($user->email_verified_at)
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">✉️
                                Email vérifié</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 border-t border-gray-100">
                <div class="px-6 py-4 text-center border-r border-gray-100">
                    <p class="text-2xl font-bold text-gray-900">{{ $invoiceCount }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Factures</p>
                </div>
                <div class="px-6 py-4 text-center sm:border-r border-gray-100">
                    <p class="text-2xl font-bold text-gray-900">{{ $clientCount }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Clients</p>
                </div>
                <div class="px-6 py-4 text-center border-r border-t sm:border-t-0 border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">{{ $memberSince }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Membre depuis</p>
                </div>
                <div class="px-6 py-4 text-center border-t sm:border-t-0 border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">{{ $lastLogin }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Dernière connexion</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
         SUCCESS / ERROR MESSAGES
    ═══════════════════════════════════════════════ --}}
        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════
         NAVIGATION TABS
    ═══════════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex overflow-x-auto scrollbar-hide">
                <button @click="activeTab = 'personal'"
                    :class="activeTab === 'personal' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informations
                </button>
                <button @click="activeTab = 'company'"
                    :class="activeTab === 'company' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Entreprise
                </button>
                <button @click="activeTab = 'security'"
                    :class="activeTab === 'security' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Sécurité
                </button>
                <button @click="activeTab = 'plan'"
                    :class="activeTab === 'plan' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    Mon Plan
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
         FORM (wraps personal + company + security tabs)
    ═══════════════════════════════════════════════ --}}
        <form method="POST" action="{{ route('client.profile.update') }}">
            @csrf
            @method('PUT')

            {{-- ────────────────────────────────────────
             TAB 1: INFORMATIONS PERSONNELLES
        ──────────────────────────────────────── --}}
            <div x-show="activeTab === 'personal'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>👤</span> Informations personnelles
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">Vos informations de base et coordonnées</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nom complet --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet
                                    <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $user->name) }}" required
                                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('name') border-red-300 @enderror"
                                        placeholder="Votre nom complet">
                                </div>
                                @error('name')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Adresse email
                                    <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $user->email) }}" required
                                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('email') border-red-300 @enderror"
                                        placeholder="votre@email.com">
                                </div>
                                @error('email')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div>
                                <label for="phone"
                                    class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input type="tel" name="phone" id="phone"
                                        value="{{ old('phone', $user->phone) }}"
                                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        placeholder="+221 77 123 45 67">
                                </div>
                            </div>

                            {{-- Adresse --}}
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="address" id="address"
                                        value="{{ old('address', $user->address) }}"
                                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        placeholder="123 Rue de Dakar, Sénégal">
                                </div>
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-blue-700"><strong>Note :</strong> Votre email est utilisé pour la
                                connexion et la réception des notifications. Assurez-vous d'y avoir accès avant de le
                                modifier.</p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('client.index') }}"
                            class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">Annuler</a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:scale-[1.02] transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>

            {{-- ────────────────────────────────────────
             TAB 2: ENTREPRISE & MOBILE MONEY
        ──────────────────────────────────────── --}}
            <div x-show="activeTab === 'company'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><span>🏢</span>
                            Informations entreprise</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Ces informations apparaîtront sur vos factures</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nom entreprise --}}
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Nom de
                                    l'entreprise</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <input type="text" name="company_name" id="company_name"
                                        value="{{ old('company_name', $user->company_name) }}"
                                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        placeholder="Ma Société SARL">
                                </div>
                            </div>

                            {{-- Numéro fiscal --}}
                            <div>
                                <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">Numéro fiscal /
                                    RCCM</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="tax_id" id="tax_id"
                                        value="{{ old('tax_id', $user->tax_id) }}"
                                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        placeholder="SN-DKR-2024-B-1234">
                                </div>
                            </div>
                        </div>

                        {{-- Tenant info --}}
                        @if ($tenant)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Espace de travail
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Nom</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $tenant->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Slug</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $tenant->slug ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Plan</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $planNames[$tenant->plan ?? 'starter'] ?? 'Starter' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Mobile Money Numbers --}}
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                                💳 Numéros de paiement mobile
                                <span class="text-xs text-gray-400 font-normal">(apparaîtront sur vos factures PDF)</span>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Wave --}}
                                <div
                                    class="p-4 bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl border border-cyan-200">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-lg">🌊</span>
                                        <span class="text-sm font-semibold text-gray-800">Wave</span>
                                    </div>
                                    <input type="tel" name="wave_number"
                                        value="{{ old('wave_number', $user->wave_number) }}"
                                        class="w-full px-3 py-2 border border-cyan-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 bg-white"
                                        placeholder="+221 77 123 45 67">
                                </div>
                                {{-- Orange Money --}}
                                <div
                                    class="p-4 bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl border border-orange-200">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-lg">🍊</span>
                                        <span class="text-sm font-semibold text-gray-800">Orange Money</span>
                                    </div>
                                    <input type="tel" name="orange_money_number"
                                        value="{{ old('orange_money_number', $user->orange_money_number) }}"
                                        class="w-full px-3 py-2 border border-orange-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white"
                                        placeholder="+221 77 123 45 67">
                                </div>
                                {{-- MTN MoMo --}}
                                <div
                                    class="p-4 bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl border border-yellow-300">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-lg">📱</span>
                                        <span class="text-sm font-semibold text-gray-800">MTN MoMo</span>
                                    </div>
                                    <input type="tel" name="momo_number"
                                        value="{{ old('momo_number', $user->momo_number) }}"
                                        class="w-full px-3 py-2 border border-yellow-200 rounded-lg text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white"
                                        placeholder="+225 07 12 34 56 78">
                                </div>
                                {{-- Moov Money --}}
                                <div
                                    class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-lg">📲</span>
                                        <span class="text-sm font-semibold text-gray-800">Moov Money</span>
                                    </div>
                                    <input type="tel" name="moov_money_number"
                                        value="{{ old('moov_money_number', $user->moov_money_number) }}"
                                        class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                                        placeholder="+229 97 12 34 56">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('client.index') }}"
                            class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">Annuler</a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:scale-[1.02] transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>

            {{-- ────────────────────────────────────────
             TAB 3: SÉCURITÉ
        ──────────────────────────────────────── --}}
            <div x-show="activeTab === 'security'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-6">
                    {{-- 2FA Status --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><span>🔐</span>
                                Authentification à deux facteurs</h2>
                        </div>
                        <div class="p-6">
                            @if (in_array($plan, ['pro', 'enterprise']))
                                @if ($has2FA)
                                    <div
                                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                                                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-green-800 text-lg">2FA Activé ✓</h3>
                                                <p class="text-sm text-gray-500">Votre compte est protégé par une double
                                                    authentification</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('client.two-factor.recovery-codes') }}"
                                            class="px-4 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium">Codes
                                            de récupération</a>
                                    </div>
                                @else
                                    <div
                                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center">
                                                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-gray-900 text-lg">2FA Non activé</h3>
                                                <p class="text-sm text-gray-500">Activez l'authentification à deux facteurs
                                                    pour sécuriser votre compte</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('client.two-factor.enable') }}"
                                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors text-sm font-semibold">Activer
                                            2FA</a>
                                    </div>
                                @endif
                            @else
                                {{-- Starter plan: upgrade CTA --}}
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">2FA — Plan Pro requis</h3>
                                            <p class="text-sm text-gray-500">L'authentification à deux facteurs est
                                                disponible à partir du plan Pro.</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('client.billing') }}"
                                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors text-sm font-semibold whitespace-nowrap">
                                        Débloquer le plan Pro →
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Change Password --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><span>🔑</span> Changer
                                le mot de passe</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Laissez vide si vous ne souhaitez pas modifier votre
                                mot de passe</p>
                        </div>

                        <div class="p-6">
                            <button type="button" @click="showPasswordFields = !showPasswordFields"
                                class="flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors mb-4"
                                x-show="!showPasswordFields">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Modifier mon mot de passe
                            </button>

                            <div x-show="showPasswordFields" x-cloak x-transition class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Mot
                                        de passe actuel</label>
                                    <input type="password" name="current_password" id="current_password"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('current_password') border-red-300 @enderror"
                                        placeholder="••••••••">
                                    @error('current_password')
                                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nouveau
                                            mot de passe</label>
                                        <input type="password" name="password" id="password"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('password') border-red-300 @enderror"
                                            placeholder="••••••••">
                                        @error('password')
                                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-400">Minimum 8 caractères</p>
                                    </div>
                                    <div>
                                        <label for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700 mb-2">Confirmer le nouveau mot
                                            de passe</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                            placeholder="••••••••">
                                    </div>
                                </div>

                                <button type="button" @click="showPasswordFields = false"
                                    class="text-sm text-gray-500 hover:text-gray-700 transition-colors">← Annuler</button>
                            </div>
                        </div>

                        <div x-show="showPasswordFields"
                            class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                            <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:scale-[1.02] transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Mettre à jour le mot de passe
                            </button>
                        </div>
                    </div>

                    {{-- Email Verification Status --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><span>✉️</span>
                                Vérification du compte</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-4">
                                @if ($user->email_verified_at)
                                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-green-800">Email vérifié</h3>
                                        <p class="text-sm text-gray-500">Vérifié le
                                            {{ \Carbon\Carbon::parse($user->email_verified_at)->translatedFormat('d F Y à H:i') }}
                                        </p>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-amber-800">Email non vérifié</h3>
                                        <p class="text-sm text-gray-500">Vérifiez votre boîte email pour le lien de
                                            confirmation</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- ────────────────────────────────────────
         TAB 4: MON PLAN (outside form)
    ──────────────────────────────────────── --}}
        <div x-show="activeTab === 'plan'" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="space-y-6">
                {{-- Current Plan Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r {{ $currentPlan['gradient'] }} border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span>{{ $currentPlan['icon'] }}</span> Plan {{ $planNames[$plan] ?? 'Starter' }}
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                            <div>
                                <h3 class="text-3xl font-black text-gray-900">
                                    @if (($planConfig['price'] ?? 0) === 0)
                                        Gratuit
                                    @else
                                        {{ number_format($planConfig['price'] ?? 0, 0, ',', ' ') }}
                                        <span class="text-base font-normal text-gray-500">XOF / mois</span>
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $planConfig['description'] ?? '' }}</p>
                            </div>
                            <a href="{{ route('client.billing') }}"
                                class="inline-flex px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition-all">
                                @if ($plan === 'enterprise')
                                    Gérer mon plan
                                @else
                                    Changer de plan
                                @endif
                            </a>
                        </div>

                        {{-- Limits --}}
                        @php $limits = $planConfig['limits'] ?? []; @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ ($limits['invoices_per_month'] ?? 0) == -1 ? '∞' : $limits['invoices_per_month'] ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Factures / mois</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ ($limits['clients'] ?? 0) == -1 ? '∞' : $limits['clients'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Clients</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ ($limits['products'] ?? 0) == -1 ? '∞' : $limits['products'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Produits</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ number_format(($limits['storage_mb'] ?? 0) / 1000, 0) }} Go</p>
                                <p class="text-xs text-gray-500 mt-1">Stockage</p>
                            </div>
                        </div>

                        {{-- Features Checklist --}}
                        @php
                            $allFeatureLabels = [
                                'basic_invoicing' => 'Facturation de base',
                                'pdf_export' => 'Export PDF',
                                'email_sending' => 'Envoi par email',
                                'client_management' => 'Gestion des clients',
                                'product_catalog' => 'Catalogue produits',
                                'analytics' => 'Analytiques & rapports',
                                'custom_templates' => 'Templates personnalisés',
                                'two_factor_auth' => 'Sécurité 2FA',
                                'multi_currency' => 'Multi-devises',
                                'recurring_invoices' => 'Factures récurrentes',
                                'payment_reminders' => 'Relances automatiques',
                                'priority_support' => 'Support prioritaire',
                                'team_management' => "Gestion d'équipe",
                                'api_access' => 'Accès API',
                                'white_label' => 'White Label',
                            ];
                            $features = $planConfig['features'] ?? [];
                        @endphp

                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Fonctionnalités incluses</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($allFeatureLabels as $key => $label)
                                <div class="flex items-center gap-2 py-1.5">
                                    @if ($features[$key] ?? false)
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $label }}</span>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span class="text-sm text-gray-400">{{ $label }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Upgrade Banner --}}
                @if ($plan !== 'enterprise')
                    <div
                        class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold">
                                    @if ($plan === 'starter')
                                        🚀 Passez au plan Pro
                                    @else
                                        🏆 Passez au plan Enterprise
                                    @endif
                                </h3>
                                <p class="text-white/80 text-sm mt-1">
                                    @if ($plan === 'starter')
                                        Clients illimités, analytiques, templates Pro, 2FA, multi-devises et bien plus !
                                    @else
                                        Gestion d'équipe, accès API, white label et limites illimitées !
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('client.billing') }}"
                                class="flex-shrink-0 px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all shadow-lg text-center">
                                Voir les plans →
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection
