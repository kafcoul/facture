@extends('layouts.guest')

@section('title', 'Vérification email')

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

        <div id="authParticles" class="absolute inset-0 overflow-hidden pointer-events-none"></div>

        {{-- ===== CENTERED LAYOUT ===== --}}
        <div class="relative flex items-center justify-center min-h-[calc(100vh-72px)] py-10 px-5 sm:px-8">
            <div class="w-full max-w-[520px]">

                {{-- Header --}}
                <div class="auth-reveal text-center mb-8">
                    {{-- Animated Email Icon --}}
                    <div
                        class="auth-icon-pulse inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 mb-6 shadow-xl shadow-brand-500/25">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>

                    <div class="flex items-center gap-2 justify-center mb-2">
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            Vérification requise
                        </span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold auth-gradient-text">Vérifiez votre email</h2>
                    <p class="mt-3 text-slate-500 text-base max-w-md mx-auto">
                        Nous avons envoyé un lien de vérification à <strong
                            class="text-slate-700">{{ auth()->user()->email ?? '' }}</strong>. Cliquez sur le lien pour
                        activer votre compte.
                    </p>
                </div>

                {{-- Success Message --}}
                @if (session('status') == 'verification-link-sent')
                    <div
                        class="auth-reveal mb-6 bg-accent-50 border border-accent-200 rounded-2xl p-4 flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-accent-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-accent-600" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-accent-800">Nouveau lien envoyé !</p>
                            <p class="text-xs text-accent-600 mt-0.5">Un nouveau lien de vérification a été envoyé à votre
                                adresse email.</p>
                        </div>
                    </div>
                @endif

                {{-- Card --}}
                <div class="auth-reveal-scale auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl shadow-elevated border border-white/60 p-8 sm:p-10"
                    style="transition-delay: 120ms">

                    {{-- Illustration --}}
                    <div class="text-center mb-8">
                        <div class="relative inline-block">
                            {{-- Email animation --}}
                            <div class="w-32 h-24 mx-auto relative">
                                {{-- Envelope body --}}
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-brand-100 to-brand-50 rounded-xl border-2 border-brand-200 shadow-lg">
                                </div>
                                {{-- Envelope flap --}}
                                <div class="absolute top-0 left-0 right-0 h-12 overflow-hidden">
                                    <div class="w-full h-full bg-gradient-to-b from-brand-200 to-brand-100 rounded-t-xl"
                                        style="clip-path: polygon(0 0, 50% 100%, 100% 0)"></div>
                                </div>
                                {{-- Checkmark inside --}}
                                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-accent-500 flex items-center justify-center shadow-md shadow-accent-500/30"
                                    style="animation: pulse 2s ease-in-out infinite">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="3"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Steps --}}
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-brand-50/50 border border-brand-100/50">
                            <div
                                class="flex-shrink-0 w-7 h-7 rounded-full bg-brand-500 text-white flex items-center justify-center text-xs font-bold">
                                1</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Vérifiez votre boîte de réception</p>
                                <p class="text-xs text-slate-500 mt-0.5">Cherchez un email de InvoiceSaaS</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50/80 border border-slate-100">
                            <div
                                class="flex-shrink-0 w-7 h-7 rounded-full bg-slate-300 text-white flex items-center justify-center text-xs font-bold">
                                2</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Cliquez sur le lien</p>
                                <p class="text-xs text-slate-500 mt-0.5">Le lien est valide pendant 60 minutes</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50/80 border border-slate-100">
                            <div
                                class="flex-shrink-0 w-7 h-7 rounded-full bg-slate-300 text-white flex items-center justify-center text-xs font-bold">
                                3</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Accédez à votre espace</p>
                                <p class="text-xs text-slate-500 mt-0.5">Votre compte sera activé automatiquement</p>
                            </div>
                        </div>
                    </div>

                    {{-- Resend Button --}}
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="btn-primary btn-shine auth-shimmer w-full flex justify-center items-center gap-2 py-4 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 via-brand-600 to-deep-700 hover:from-brand-700 hover:via-brand-700 hover:to-deep-800 shadow-btn cursor-pointer group relative overflow-hidden"
                            onclick="this.querySelector('.btn-text')?.classList.add('hidden'); this.querySelector('.btn-spinner')?.classList.remove('hidden'); this.disabled = true; this.closest('form').submit();">
                            <span class="btn-text flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                                </svg>
                                Renvoyer le lien de vérification
                            </span>
                            <span class="btn-spinner hidden"><span class="auth-spinner"></span></span>
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="mt-6 relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200/80"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-4 bg-white/90 text-slate-400 font-medium">ou</span>
                        </div>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="mt-5">
                        @csrf
                        <button type="submit"
                            class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border-2 border-slate-200/80 rounded-2xl text-sm font-semibold text-slate-700 bg-white/80 backdrop-blur-sm hover:bg-red-50 hover:border-red-200 hover:text-red-700 transition-all cursor-pointer group">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-red-500 transition-colors" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Se déconnecter
                        </button>
                    </form>
                </div>

                {{-- Help text --}}
                <div class="auth-reveal mt-6 text-center" style="transition-delay: 300ms">
                    <p class="text-xs text-slate-400">
                        Vous n'avez pas reçu l'email ? Vérifiez votre dossier spam ou
                        <a href="mailto:support@invoicesaas.com"
                            class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">contactez le
                            support</a>.
                    </p>
                </div>

                {{-- Trust Badges --}}
                <div class="auth-reveal mt-5" style="transition-delay: 400ms">
                    <div class="flex flex-wrap items-center justify-center gap-3 text-[11px] text-slate-400">
                        <span
                            class="auth-float-badge flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/70 backdrop-blur-sm border border-slate-100 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-accent-500" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            SSL 256-bit
                        </span>
                        <span
                            class="auth-float-badge flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/70 backdrop-blur-sm border border-slate-100 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            Données protégées
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {

            0%,
            100% {
                transform: translateX(-50%) scale(1);
                opacity: 1;
            }

            50% {
                transform: translateX(-50%) scale(1.1);
                opacity: 0.8;
            }
        }
    </style>
@endsection
