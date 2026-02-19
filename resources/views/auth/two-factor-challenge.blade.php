@extends('layouts.guest')

@section('title', 'Vérification à deux facteurs')

@section('content')
    <div class="relative min-h-[calc(100vh-72px)] overflow-hidden">

        {{-- ===== ANIMATED BACKGROUNDS ===== --}}
        <div class="absolute inset-0 auth-mesh"></div>
        <div class="auth-aurora"></div>
        <div class="absolute inset-0 auth-dots opacity-30"></div>

        <div class="auth-blob auth-blob-1"></div>
        <div class="auth-blob auth-blob-2"></div>

        <div id="authParticles" class="absolute inset-0 overflow-hidden pointer-events-none"></div>

        {{-- ===== CENTERED LAYOUT ===== --}}
        <div class="relative flex items-center justify-center min-h-[calc(100vh-72px)] py-10 px-5 sm:px-8">
            <div class="w-full max-w-[460px]" x-data="{ recovery: false }">

                {{-- Header --}}
                <div class="auth-reveal text-center mb-8">
                    <div
                        class="auth-icon-pulse inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 mb-5 shadow-lg shadow-amber-500/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>

                    <div class="flex items-center gap-2 justify-center mb-2">
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            Authentification à deux facteurs
                        </span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold auth-gradient-text">Vérification 2FA</h2>
                    <p class="mt-2 text-slate-500 text-base" x-show="!recovery">
                        Entrez le code à 6 chiffres de votre application d'authentification.
                    </p>
                    <p class="mt-2 text-slate-500 text-base" x-show="recovery" x-cloak>
                        Entrez l'un de vos codes de récupération d'urgence.
                    </p>
                </div>

                {{-- Form Card --}}
                <div class="auth-reveal-scale auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl shadow-elevated border border-white/60 p-8 sm:p-10"
                    style="transition-delay: 120ms">

                    <form method="POST" action="{{ route('two-factor.login.store') }}" class="auth-stagger space-y-5">
                        @csrf

                        {{-- Code TOTP --}}
                        <div x-show="!recovery" class="auth-reveal" style="transition-delay: 200ms">
                            <label for="code" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Code d'authentification
                            </label>
                            <div class="auth-input-wrap relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="auth-icon h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                    </svg>
                                </div>
                                <input id="code" name="code" type="text" inputmode="numeric"
                                    autocomplete="one-time-code" autofocus maxlength="6" pattern="[0-9]{6}"
                                    @class([
                                        'block w-full pl-10 pr-4 py-3.5 bg-surface/80 border rounded-2xl text-center font-mono text-lg tracking-[0.5em] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all',
                                        'border-red-400 ring-2 ring-red-100' => $errors->has('code'),
                                        'border-slate-200' => !$errors->has('code'),
                                    ]) placeholder="000000">
                            </div>
                            @error('code')
                                <p class="mt-1.5 text-xs text-red-500 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Recovery Code --}}
                        <div x-show="recovery" x-cloak class="auth-reveal" style="transition-delay: 200ms">
                            <label for="recovery_code" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Code de récupération
                            </label>
                            <div class="auth-input-wrap relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="auth-icon h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <input id="recovery_code" name="recovery_code" type="text" autocomplete="off"
                                    @class([
                                        'block w-full pl-10 pr-4 py-3.5 bg-surface/80 border rounded-2xl text-sm font-mono tracking-wider placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all',
                                        'border-red-400 ring-2 ring-red-100' => $errors->has('recovery_code'),
                                        'border-slate-200' => !$errors->has('recovery_code'),
                                    ]) placeholder="xxxxx-xxxxx">
                            </div>
                            @error('recovery_code')
                                <p class="mt-1.5 text-xs text-red-500 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Toggle recovery / code --}}
                        <div class="text-center">
                            <button type="button"
                                class="text-sm text-amber-600 hover:text-amber-700 font-medium hover:underline transition-colors cursor-pointer"
                                x-show="!recovery"
                                @click.prevent="recovery = true; $nextTick(() => document.getElementById('recovery_code').focus())">
                                Utiliser un code de récupération
                            </button>
                            <button type="button"
                                class="text-sm text-amber-600 hover:text-amber-700 font-medium hover:underline transition-colors cursor-pointer"
                                x-show="recovery" x-cloak
                                @click.prevent="recovery = false; $nextTick(() => document.getElementById('code').focus())">
                                Utiliser le code d'authentification
                            </button>
                        </div>

                        {{-- Submit --}}
                        <div class="auth-reveal" style="transition-delay: 300ms">
                            <button type="submit"
                                class="auth-btn-glow group relative w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 shadow-lg shadow-amber-500/25 hover:shadow-xl hover:shadow-amber-500/30 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-300 hover:scale-[1.01] active:scale-[0.99]">
                                <svg class="w-4 h-4 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                                <span x-show="!recovery">Vérifier le code</span>
                                <span x-show="recovery" x-cloak>Utiliser le code de récupération</span>
                                <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-0.5"
                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Divider --}}
                    <div class="relative flex items-center my-6">
                        <div class="flex-grow border-t border-slate-200/80"></div>
                        <span class="flex-shrink mx-3 text-xs text-slate-400">Aide</span>
                        <div class="flex-grow border-t border-slate-200/80"></div>
                    </div>

                    {{-- Help info --}}
                    <div class="bg-amber-50/50 rounded-2xl p-4 border border-amber-100/60">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-amber-800 font-semibold mb-1">Comment obtenir votre code ?</p>
                                <p class="text-xs text-amber-700/80">
                                    Ouvrez votre application d'authentification (Google Authenticator, Authy, etc.)
                                    et entrez le code à 6 chiffres affiché pour <strong>InvoiceSaaS</strong>.
                                    Le code se renouvelle toutes les 30 secondes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer link --}}
                <div class="auth-reveal text-center mt-6" style="transition-delay: 300ms">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-amber-600 transition-colors group">
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Retour à la connexion
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
