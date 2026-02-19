@extends('layouts.guest')

@section('title', 'Confirmer le mot de passe')

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
            <div class="w-full max-w-[460px]">

                {{-- Header --}}
                <div class="auth-reveal text-center mb-8">
                    <div
                        class="auth-icon-pulse inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 mb-5 shadow-lg shadow-amber-500/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>

                    <div class="flex items-center gap-2 justify-center mb-2">
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            Zone sécurisée
                        </span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold auth-gradient-text">Confirmez votre mot de passe</h2>
                    <p class="mt-2 text-slate-500 text-base">Veuillez confirmer votre mot de passe avant de continuer.</p>
                </div>

                {{-- Form Card --}}
                <div class="auth-reveal-scale auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl shadow-elevated border border-white/60 p-8 sm:p-10"
                    style="transition-delay: 120ms">

                    <form method="POST" action="{{ route('password.confirm') }}" class="auth-stagger space-y-5"
                        x-data="{ show: false }">
                        @csrf

                        {{-- Password --}}
                        <div class="auth-reveal" style="transition-delay: 200ms">
                            <label for="password" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Mot de passe
                            </label>
                            <div class="auth-input-wrap relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="auth-icon h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <input id="password" name="password" :type="show ? 'text' : 'password'"
                                    autocomplete="current-password" required @class([
                                        'block w-full pl-10 pr-12 py-3.5 bg-surface/80 border rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all',
                                        'border-red-400 ring-2 ring-red-100' => $errors->has('password'),
                                        'border-slate-200' => !$errors->has('password'),
                                    ])
                                    placeholder="Votre mot de passe actuel">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-brand-600 transition-colors cursor-pointer">
                                    <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="h-4 w-4" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
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

                        {{-- Submit --}}
                        <div class="auth-reveal" style="transition-delay: 280ms">
                            <button type="submit"
                                class="btn-primary btn-shine auth-shimmer w-full flex justify-center items-center gap-2 py-4 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 via-brand-600 to-deep-700 hover:from-brand-700 hover:via-brand-700 hover:to-deep-800 shadow-btn cursor-pointer group relative overflow-hidden">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                    Confirmer
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
