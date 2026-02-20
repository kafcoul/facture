@extends('layouts.dashboard')

@section('title', 'Paramètres')

@section('content')
    <div x-data="{ activeTab: 'company' }" class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Paramètres
            </h1>
            <p class="mt-1 text-gray-500">Configurez votre compte et vos préférences de facturation</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center text-green-800">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Navigation onglets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
            <div class="flex overflow-x-auto scrollbar-hide">
                <button @click="activeTab = 'company'"
                    :class="activeTab === 'company' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="flex items-center px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Entreprise
                </button>
                <button @click="activeTab = 'payment'"
                    :class="activeTab === 'payment' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="flex items-center px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Paiement Mobile
                </button>
                <button @click="activeTab = 'templates'"
                    :class="activeTab === 'templates' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="flex items-center px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z">
                        </path>
                    </svg>
                    Templates
                </button>
                <button @click="activeTab = 'security'"
                    :class="activeTab === 'security' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="flex items-center px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    Sécurité
                </button>
            </div>
        </div>

        <!-- Contenu des onglets -->
        <form action="{{ route('client.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Onglet Entreprise -->
            <div x-show="activeTab === 'company'" x-cloak>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Informations de l'entreprise</h2>
                        <p class="text-sm text-gray-500">Ces informations apparaîtront sur vos factures</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom de l'entreprise</label>
                                <input type="text" name="company_name"
                                    value="{{ old('company_name', auth()->user()->company_name) }}"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ma Société SARL">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Numéro fiscal / RCCM</label>
                                <input type="text" name="tax_id" value="{{ old('tax_id', auth()->user()->tax_id) }}"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="SN-DKR-2024-B-1234">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse complète</label>
                            <textarea name="address" rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                placeholder="123 Rue de l'Exemple, Dakar, Sénégal">{{ old('address', auth()->user()->address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="+221 77 123 45 67">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email (non modifiable)</label>
                                <input type="email" value="{{ auth()->user()->email }}" disabled
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Paiement Mobile -->
            <div x-show="activeTab === 'payment'" x-cloak>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">💳 Paiement Mobile Money</h2>
                        <p class="text-sm text-gray-500">Ajoutez vos numéros pour recevoir des paiements via Mobile Money
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Wave -->
                            <div class="p-5 bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl border-2 border-cyan-200">
                                <div class="flex items-center mb-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
                                        🌊
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900">Wave</h3>
                                        <p class="text-xs text-gray-500">Sénégal, Côte d'Ivoire</p>
                                    </div>
                                </div>
                                <input type="tel" name="wave_number"
                                    value="{{ old('wave_number', auth()->user()->wave_number) }}"
                                    class="w-full px-4 py-3 border-2 border-cyan-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 bg-white"
                                    placeholder="+221 77 123 45 67">
                            </div>

                            <!-- Orange Money -->
                            <div
                                class="p-5 bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl border-2 border-orange-200">
                                <div class="flex items-center mb-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-orange-400 to-amber-500 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
                                        🍊
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900">Orange Money</h3>
                                        <p class="text-xs text-gray-500">Le plus populaire en Afrique</p>
                                    </div>
                                </div>
                                <input type="tel" name="orange_money_number"
                                    value="{{ old('orange_money_number', auth()->user()->orange_money_number) }}"
                                    class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white"
                                    placeholder="+221 77 123 45 67">
                            </div>

                            <!-- MTN MoMo -->
                            <div
                                class="p-5 bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl border-2 border-yellow-300">
                                <div class="flex items-center mb-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
                                        📱
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900">MTN Mobile Money</h3>
                                        <p class="text-xs text-gray-500">MoMo - Côte d'Ivoire, Ghana</p>
                                    </div>
                                </div>
                                <input type="tel" name="momo_number"
                                    value="{{ old('momo_number', auth()->user()->momo_number) }}"
                                    class="w-full px-4 py-3 border-2 border-yellow-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white"
                                    placeholder="+225 07 12 34 56 78">
                            </div>

                            <!-- Moov Money -->
                            <div
                                class="p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                                <div class="flex items-center mb-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
                                        📲
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900">Moov Money</h3>
                                        <p class="text-xs text-gray-500">Bénin, Togo, Niger</p>
                                    </div>
                                </div>
                                <input type="tel" name="moov_money_number"
                                    value="{{ old('moov_money_number', auth()->user()->moov_money_number) }}"
                                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                                    placeholder="+229 97 12 34 56">
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-indigo-800">
                                        <strong>Comment ça marche ?</strong><br>
                                        Vos numéros de paiement mobile apparaîtront sur vos factures PDF avec un <strong>QR
                                            code</strong>.
                                        Vos clients pourront scanner ce QR code ou utiliser ces numéros pour vous payer
                                        directement.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Templates -->
            <div x-show="activeTab === 'templates'" x-cloak>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">🎨 Templates de factures</h2>
                        <p class="text-sm text-gray-500">Choisissez le design de vos factures PDF</p>
                    </div>
                    <div class="p-6">
                        @php
                            $currentTemplate = auth()->user()->invoice_template ?? 'classic';
                            $templateNames = [
                                'classic' => 'Classique',
                                'modern' => 'Moderne',
                                'minimal' => 'Minimaliste',
                                'corporate' => 'Corporate',
                                'creative' => 'Créatif',
                                'elegant' => 'Élégant',
                                'premium' => 'Premium',
                                'african' => 'Africain',
                            ];
                        @endphp

                        <div class="text-center py-8">
                            <div
                                class="w-20 h-20 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="text-4xl">🎨</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                Template actuel : <span
                                    class="text-indigo-600">{{ $templateNames[$currentTemplate] ?? $currentTemplate }}</span>
                            </h3>
                            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                                Parcourez les 8 templates disponibles avec prévisualisation en temps réel sur la page
                                dédiée.
                            </p>
                            <a href="{{ route('client.templates.index') }}"
                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-[1.02] transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z">
                                    </path>
                                </svg>
                                Choisir un template
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Sécurité -->
            <div x-show="activeTab === 'security'" x-cloak>
                <div class="space-y-6">
                    <!-- 2FA -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">🔐 Authentification à deux facteurs</h2>
                            <p class="text-sm text-gray-500">Sécurisez votre compte avec une vérification supplémentaire
                            </p>
                        </div>
                        <div class="p-6">
                            @php
                                $userPlanSecurity = auth()->user()->plan ?? 'starter';
                                $canUse2FA = in_array($userPlanSecurity, ['pro', 'enterprise']);
                            @endphp

                            @if (!$canUse2FA)
                                {{-- Starter: upgrade needed --}}
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-gray-900">2FA — Plan Pro requis</h3>
                                            <p class="text-sm text-gray-500">L'authentification à deux facteurs est
                                                disponible à partir du plan Pro.</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('client.billing') }}"
                                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all">
                                        Voir les plans
                                    </a>
                                </div>
                            @elseif (auth()->user()->two_factor_secret)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-green-800">2FA Activé ✓</h3>
                                            <p class="text-sm text-gray-500">Votre compte est protégé</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('client.two-factor.recovery-codes') }}"
                                            class="px-4 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                                            Codes de récup.
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-gray-900">2FA Désactivé</h3>
                                            <p class="text-sm text-gray-500">Activez pour plus de sécurité</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('client.two-factor.enable') }}"
                                        class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
                                        Activer 2FA
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Changer mot de passe -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">🔑 Changer le mot de passe</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe actuel</label>
                                <input type="password" name="current_password"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de
                                        passe</label>
                                    <input type="password" name="new_password"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer</label>
                                    <input type="password" name="new_password_confirmation"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zone danger -->
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-red-200 overflow-hidden">
                        <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                            <h2 class="text-lg font-semibold text-red-900">⚠️ Zone de danger</h2>
                        </div>
                        <div class="p-6" x-data="{ confirmDelete: false }">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900">Supprimer mon compte</h3>
                                    <p class="text-sm text-gray-500">Cette action est irréversible. Toutes vos données
                                        seront supprimées.</p>
                                </div>
                                <button type="button" @click="confirmDelete = true"
                                    class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                                    Supprimer
                                </button>
                            </div>

                            {{-- Confirmation modal --}}
                            <div x-show="confirmDelete" x-cloak
                                class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                                <p class="text-sm text-red-800 font-medium mb-3">
                                    ⚠️ Êtes-vous absolument sûr(e) ? Cette action supprimera définitivement :
                                </p>
                                <ul class="text-sm text-red-700 list-disc list-inside mb-4 space-y-1">
                                    <li>Votre compte et profil</li>
                                    <li>Toutes vos factures et données</li>
                                    <li>Vos clients et produits</li>
                                </ul>
                                <p class="text-xs text-red-600 mb-4">Pour confirmer, contactez le support à
                                    <strong>support@invoicesaas.com</strong></p>
                                <div class="flex gap-3">
                                    <button type="button" @click="confirmDelete = false"
                                        class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                                        Annuler
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton Sauvegarder -->
            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-[1.02] transition-all duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Sauvegarder les modifications
                </button>
            </div>
        </form>
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
