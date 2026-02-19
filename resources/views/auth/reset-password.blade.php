@extends('layouts.guest')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
    <div class="relative min-h-[calc(100vh-72px)] overflow-hidden">

        {{-- ===== ANIMATED BACKGROUNDS ===== --}}
        <div class="absolute inset-0 auth-mesh"></div>
        <div class="auth-aurora"></div>
        <div class="absolute inset-0 auth-dots opacity-30"></div>
        <div class="absolute inset-0 auth-grid opacity-40"></div>

        {{-- Morphing blobs --}}
        <div class="auth-blob auth-blob-1"></div>
        <div class="auth-blob auth-blob-2"></div>
        <div class="auth-blob auth-blob-3"></div>

        {{-- Floating particles --}}
        <div id="authParticles" class="absolute inset-0 overflow-hidden pointer-events-none"></div>

        {{-- ===== SPLIT PANEL LAYOUT ===== --}}
        <div class="relative flex min-h-[calc(100vh-72px)]">

            {{-- ████ LEFT PANEL — Branding ████ --}}
            <div class="hidden lg:flex lg:w-[48%] xl:w-[45%] auth-panel-left flex-col justify-between p-10 xl:p-14">

                {{-- Decorative shapes --}}
                <div class="panel-shape panel-shape-1" style="--r:15deg"></div>
                <div class="panel-shape panel-shape-2" style="--r:-20deg"></div>
                <div class="panel-shape panel-shape-3" style="--r:45deg"></div>
                <div class="panel-shape panel-shape-4" style="--r:-10deg"></div>

                {{-- Animated lines --}}
                <div class="panel-line panel-line-1"></div>
                <div class="panel-line panel-line-2"></div>
                <div class="panel-line panel-line-3"></div>

                {{-- Expanding rings --}}
                <div class="auth-ring" style="width:200px;height:200px;top:15%;right:10%;animation-delay:0s"></div>
                <div class="auth-ring" style="width:150px;height:150px;bottom:20%;left:8%;animation-delay:3s"></div>

                <div id="panelParticles" class="absolute inset-0 pointer-events-none"></div>

                {{-- Top — Logo --}}
                <div class="relative z-10 auth-reveal" style="transition-delay: 200ms">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/10">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <span class="text-white/90 text-lg font-bold tracking-tight">InvoiceSaaS</span>
                    </div>
                </div>

                {{-- Center --}}
                <div class="relative z-10 panel-stagger flex-1 flex flex-col justify-center -mt-8">
                    <div class="auth-reveal mb-10" style="transition-delay: 400ms">
                        <div class="relative w-full max-w-[280px] mx-auto">
                            <div class="auth-float-badge bg-white/[.07] backdrop-blur-sm rounded-2xl border border-white/[.08] p-8 shadow-2xl shadow-black/20 text-center"
                                style="animation-delay: 0s">
                                <div
                                    class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-accent-400/30 to-brand-400/20 flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-white/80" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-2 w-32 bg-white/15 rounded-full mx-auto"></div>
                                    <div class="h-1.5 w-24 bg-white/10 rounded-full mx-auto"></div>
                                </div>
                            </div>

                            <div class="auth-float-badge absolute -top-3 -right-3 bg-brand-500/90 backdrop-blur-sm text-white text-[11px] font-bold px-3 py-1.5 rounded-full shadow-lg shadow-brand-500/30 flex items-center gap-1.5"
                                style="animation-delay: 1s">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75" />
                                </svg>
                                Nouveau MDP
                            </div>
                        </div>
                    </div>

                    <div class="auth-reveal text-center" style="transition-delay: 550ms">
                        <h2 class="text-2xl xl:text-3xl font-extrabold text-white leading-tight">
                            Créez un nouveau<br>
                            <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-brand-300 via-purple-300 to-accent-300">mot
                                de passe sécurisé</span>
                        </h2>
                        <p class="mt-3 text-sm text-white/50 max-w-xs mx-auto leading-relaxed">
                            Choisissez un mot de passe fort pour protéger votre compte.
                        </p>
                    </div>
                </div>

                {{-- Bottom --}}
                <div class="relative z-10 auth-reveal" style="transition-delay: 900ms">
                    <div class="bg-white/[.06] backdrop-blur-sm rounded-2xl border border-white/[.08] p-5">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex-shrink-0 w-9 h-9 rounded-full bg-gradient-to-br from-accent-400 to-accent-600 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white/70 text-sm leading-relaxed">Conseils : utilisez au moins 8 caractères
                                    avec des majuscules, chiffres et symboles.</p>
                                <p class="text-white/40 text-xs mt-2 font-medium">Protection maximale garantie</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ████ RIGHT PANEL — Reset Password Form ████ --}}
            <div class="flex-1 flex items-center justify-center py-10 px-5 sm:px-8 lg:px-12 xl:px-16">
                <div class="w-full max-w-[460px]">

                    {{-- Header --}}
                    <div class="auth-reveal text-center lg:text-left mb-8">
                        <div
                            class="lg:hidden auth-icon-pulse inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 mb-5 shadow-lg shadow-brand-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>

                        <div class="flex items-center gap-2 justify-center lg:justify-start mb-2">
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-accent-600 bg-accent-50 px-3 py-1 rounded-full border border-accent-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent-500 auth-pulse-dot"></span>
                                Dernière étape
                            </span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-extrabold auth-gradient-text">Nouveau mot de passe</h2>
                        <p class="mt-2 text-slate-500 text-base">Choisissez un mot de passe fort et sécurisé.</p>
                    </div>

                    {{-- Form Card --}}
                    <div class="auth-reveal-scale auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl shadow-elevated border border-white/60 p-8 sm:p-10"
                        style="transition-delay: 120ms">

                        <form method="POST" action="{{ route('password.update') }}" class="auth-stagger space-y-5"
                            x-data="{ showPass: false, showConfirm: false }">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            {{-- Email --}}
                            <div class="auth-reveal" style="transition-delay: 200ms">
                                <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Adresse email
                                </label>
                                <div class="auth-input-wrap relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </div>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                        value="{{ old('email', $request->email) }}" readonly
                                        class="block w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-500 cursor-not-allowed">
                                </div>
                                @error('email')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- New Password --}}
                            <div class="auth-reveal" style="transition-delay: 280ms">
                                <label for="password" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Nouveau mot de passe
                                </label>
                                <div class="auth-input-wrap relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </div>
                                    <input id="password" name="password" :type="showPass ? 'text' : 'password'"
                                        autocomplete="new-password" required @class([
                                            'block w-full pl-10 pr-12 py-3.5 bg-surface/80 border rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all',
                                            'border-red-400 ring-2 ring-red-100' => $errors->has('password'),
                                            'border-slate-200' => !$errors->has('password'),
                                        ])
                                        placeholder="Minimum 8 caractères">
                                    <button type="button" @click="showPass = !showPass"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-brand-600 transition-colors cursor-pointer">
                                        <svg x-show="!showPass" class="h-4 w-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg x-show="showPass" x-cloak class="h-4 w-4" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="auth-reveal" style="transition-delay: 360ms">
                                <label for="password_confirmation"
                                    class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Confirmer le mot de passe
                                </label>
                                <div class="auth-input-wrap relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                    </div>
                                    <input id="password_confirmation" name="password_confirmation"
                                        :type="showConfirm ? 'text' : 'password'" autocomplete="new-password" required
                                        class="block w-full pl-10 pr-12 py-3.5 bg-surface/80 border border-slate-200 rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all"
                                        placeholder="Retapez votre mot de passe">
                                    <button type="button" @click="showConfirm = !showConfirm"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-brand-600 transition-colors cursor-pointer">
                                        <svg x-show="!showConfirm" class="h-4 w-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg x-show="showConfirm" x-cloak class="h-4 w-4" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Password Strength Indicator --}}
                            <div class="auth-reveal" style="transition-delay: 440ms" x-data="passwordStrength()"
                                x-init="$watch('$refs.passwordField', () => {})">
                                <div class="flex gap-1.5 mb-2">
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                        :class="strength >= 1 ? (strength >= 3 ? 'bg-accent-500' : strength >= 2 ?
                                            'bg-amber-400' : 'bg-red-400') : 'bg-slate-200'">
                                    </div>
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                        :class="strength >= 2 ? (strength >= 3 ? 'bg-accent-500' : 'bg-amber-400') :
                                            'bg-slate-200'">
                                    </div>
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                        :class="strength >= 3 ? 'bg-accent-500' : 'bg-slate-200'"></div>
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                        :class="strength >= 4 ? 'bg-accent-500' : 'bg-slate-200'"></div>
                                </div>
                                <p class="text-[11px] font-medium"
                                    :class="strength >= 3 ? 'text-accent-600' : strength >= 2 ? 'text-amber-600' : strength >=
                                        1 ? 'text-red-500' : 'text-slate-400'"
                                    x-text="strengthText"></p>
                            </div>

                            {{-- Submit --}}
                            <div class="auth-reveal" style="transition-delay: 520ms">
                                <button type="submit"
                                    class="btn-primary btn-shine auth-shimmer w-full flex justify-center items-center gap-2 py-4 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 via-brand-600 to-deep-700 hover:from-brand-700 hover:via-brand-700 hover:to-deep-800 shadow-btn cursor-pointer group relative overflow-hidden"
                                    onclick="this.querySelector('.btn-text')?.classList.add('hidden'); this.querySelector('.btn-spinner')?.classList.remove('hidden'); this.disabled = true; this.closest('form').submit();">
                                    <span class="btn-text flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                        Réinitialiser le mot de passe
                                    </span>
                                    <span class="btn-spinner hidden"><span class="auth-spinner"></span></span>
                                </button>
                            </div>
                        </form>

                        {{-- Back to Login --}}
                        <div class="auth-reveal mt-7" style="transition-delay: 600ms">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-slate-200/80"></div>
                                </div>
                                <div class="relative flex justify-center text-xs">
                                    <span class="px-4 bg-white/90 text-slate-400 font-medium">Vous vous souvenez ?</span>
                                </div>
                            </div>
                            <div class="mt-5">
                                <a href="{{ route('login') }}"
                                    class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border-2 border-slate-200/80 rounded-2xl text-sm font-semibold text-slate-700 bg-white/80 backdrop-blur-sm hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700 transition-all cursor-pointer group">
                                    <svg class="w-4 h-4 text-brand-500 group-hover:scale-110 transition-transform"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                    </svg>
                                    Retour à la connexion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Password Strength Script --}}
    <script>
        function passwordStrength() {
            return {
                strength: 0,
                strengthText: 'Entrez un mot de passe',
                init() {
                    const passwordInput = document.getElementById('password');
                    if (passwordInput) {
                        passwordInput.addEventListener('input', (e) => this.check(e.target.value));
                    }
                },
                check(password) {
                    let score = 0;
                    if (password.length >= 8) score++;
                    if (/[A-Z]/.test(password)) score++;
                    if (/[0-9]/.test(password)) score++;
                    if (/[^A-Za-z0-9]/.test(password)) score++;
                    this.strength = score;
                    const texts = ['Entrez un mot de passe', 'Faible — ajoutez des majuscules et chiffres',
                        'Moyen — ajoutez des symboles', 'Fort — bonne combinaison !', 'Excellent — très sécurisé !'
                    ];
                    this.strengthText = texts[score];
                }
            }
        }
    </script>
@endsection
