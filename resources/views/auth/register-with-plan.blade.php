@extends('layouts.guest')

@section('title', 'Inscription')

@section('styles')
    <style>
        .plan-card {
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-8px);
        }

        .plan-selected {
            border-color: #818cf8 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }

        /* Register page background */
        .register-bg {
            background:
                radial-gradient(ellipse 80% 60% at 50% -20%, rgba(30, 58, 138, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 85% 15%, rgba(79, 70, 229, 0.05) 0%, transparent 50%),
                radial-gradient(ellipse 40% 50% at 10% 70%, rgba(16, 185, 129, 0.03) 0%, transparent 50%);
        }

        /* Step indicator transitions */
        .step-circle {
            transition: all .4s cubic-bezier(.16, 1, .3, 1);
        }

        .step-label {
            transition: all .3s ease;
        }

        .step-line {
            transition: all .5s ease;
            position: relative;
            overflow: hidden;
        }

        .step-line.active::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, #6366f1, #10b981);
            border-radius: inherit;
            animation: stepLineFill .6s ease forwards;
        }

        @keyframes stepLineFill {
            from {
                transform: scaleX(0);
                transform-origin: left
            }

            to {
                transform: scaleX(1);
                transform-origin: left
            }
        }

        /* Plan card shimmer on hover */
        .plan-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, transparent 40%, rgba(255, 255, 255, .04) 50%, transparent 60%);
            opacity: 0;
            transition: opacity .3s ease;
        }

        .plan-card:hover::before {
            opacity: 1;
        }

        /* Confetti for selected plan */
        .plan-selected .plan-check {
            animation: planCheckPop .4s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        @keyframes planCheckPop {
            0% {
                transform: scale(0);
                opacity: 0
            }

            60% {
                transform: scale(1.2)
            }

            100% {
                transform: scale(1);
                opacity: 1
            }
        }
    </style>
@endsection

@section('content')
    <div class="relative min-h-[calc(100vh-72px)] overflow-hidden">

        {{-- ===== ANIMATED BACKGROUNDS ===== --}}
        <div class="absolute inset-0 register-bg"></div>
        <div class="absolute inset-0 auth-dots opacity-30"></div>
        <div class="absolute inset-0 auth-grid opacity-40"></div>
        <div class="auth-aurora"></div>

        {{-- Morphing blobs --}}
        <div class="auth-blob auth-blob-1"></div>
        <div class="auth-blob auth-blob-2"></div>
        <div class="auth-blob auth-blob-3"></div>

        {{-- Floating particles --}}
        <div id="authParticles" class="absolute inset-0 overflow-hidden pointer-events-none"></div>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="relative py-12 px-4">
            <div class="max-w-6xl mx-auto">

                {{-- Header --}}
                <div class="auth-reveal text-center mb-10">
                    <div
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-accent-600 bg-accent-50 px-3 py-1 rounded-full border border-accent-100 mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-500 auth-pulse-dot"></span>
                        14 jours d'essai gratuit
                    </div>
                    <h1 class="text-4xl md:text-5xl font-extrabold auth-gradient-text mb-4">
                        Créez votre compte
                    </h1>
                    <p class="text-lg text-slate-500 max-w-md mx-auto">
                        Choisissez votre plan et commencez en 2 minutes
                    </p>
                </div>

                {{-- Steps indicator --}}
                <div class="auth-reveal flex justify-center mb-12" style="transition-delay: 100ms">
                    <div
                        class="flex items-center gap-3 sm:gap-4 bg-white/80 backdrop-blur-sm rounded-2xl px-6 py-3 shadow-card border border-slate-100">
                        <div class="flex items-center gap-2">
                            <div id="step1"
                                class="step-circle w-9 h-9 rounded-full bg-brand-600 text-white flex items-center justify-center text-sm font-bold shadow-btn">
                                1</div>
                            <span class="step-label text-sm font-semibold text-slate-800 hidden sm:inline">Plan</span>
                        </div>
                        <div id="stepLine1" class="step-line w-8 sm:w-12 h-1 bg-slate-200 rounded-full"></div>
                        <div class="flex items-center gap-2">
                            <div id="step2"
                                class="step-circle w-9 h-9 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold">
                                2</div>
                            <span id="step2Label"
                                class="step-label text-sm font-medium text-slate-400 hidden sm:inline">Informations</span>
                        </div>
                        <div id="stepLine2" class="step-line w-8 sm:w-12 h-1 bg-slate-200 rounded-full"></div>
                        <div class="flex items-center gap-2">
                            <div id="step3"
                                class="step-circle w-9 h-9 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold">
                                3</div>
                            <span id="step3Label"
                                class="step-label text-sm font-medium text-slate-400 hidden sm:inline">Confirmation</span>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('register.with-plan') }}" method="POST" id="registrationForm">
                    @csrf

                    {{-- ===== STEP 1: Choose Plan ===== --}}
                    <div id="planStep" class="auth-reveal-scale mb-8" style="transition-delay: 200ms">
                        <h2 class="text-2xl font-bold text-slate-800 text-center mb-8">Choisissez votre formule</h2>

                        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                            {{-- Starter --}}
                            <div class="plan-card auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl p-7 border-2 border-slate-200/80 cursor-pointer relative shadow-card hover:shadow-card-hover"
                                onclick="selectPlan('starter', 0)">
                                <input type="radio" name="plan" value="starter" id="plan-starter" class="hidden">
                                {{-- Check badge --}}
                                <div
                                    class="plan-check absolute top-4 right-4 w-6 h-6 rounded-full bg-brand-600 text-white items-center justify-center hidden">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div class="text-center mb-6">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.58-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-800 mb-1">Starter</h3>
                                    <div class="text-3xl font-extrabold auth-gradient-text mb-1">Gratuit</div>
                                    <p class="text-sm text-slate-400">Parfait pour débuter</p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        10 factures/mois
                                    </li>
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        5 clients
                                    </li>
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Support email
                                    </li>
                                </ul>
                            </div>

                            {{-- Pro (Popular) --}}
                            <div class="plan-card auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl p-7 border-2 border-brand-300 cursor-pointer relative shadow-elevated hover:shadow-card-hover ring-1 ring-brand-100"
                                onclick="selectPlan('pro', 19000)">
                                <div
                                    class="absolute -top-3.5 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-brand-600 to-brand-700 text-white px-4 py-1 rounded-full text-xs font-bold shadow-btn">
                                    ⭐ POPULAIRE
                                </div>
                                <input type="radio" name="plan" value="pro" id="plan-pro" class="hidden"
                                    checked>
                                {{-- Check badge --}}
                                <div
                                    class="plan-check absolute top-4 right-4 w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div class="text-center mb-6">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-800 mb-1">Pro</h3>
                                    <div class="text-3xl font-extrabold auth-gradient-text mb-1">19 000 <span
                                            class="text-lg font-semibold text-slate-400">XOF</span></div>
                                    <p class="text-sm text-slate-400">/mois</p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Factures illimitées
                                    </li>
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Clients illimités
                                    </li>
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Support prioritaire
                                    </li>
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Export PDF personnalisé
                                    </li>
                                </ul>
                            </div>

                            {{-- Enterprise --}}
                            <div class="plan-card auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl p-7 border-2 border-slate-200/80 cursor-pointer relative shadow-card hover:shadow-card-hover"
                                onclick="selectPlan('enterprise', 65000)">
                                <input type="radio" name="plan" value="enterprise" id="plan-enterprise"
                                    class="hidden">
                                {{-- Check badge --}}
                                <div
                                    class="plan-check absolute top-4 right-4 w-6 h-6 rounded-full bg-brand-600 text-white items-center justify-center hidden">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div class="text-center mb-6">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-deep-50 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-deep-700" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 7.5h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-800 mb-1">Enterprise</h3>
                                    <div class="text-3xl font-extrabold auth-gradient-text mb-1">65 000 <span
                                            class="text-lg font-semibold text-slate-400">XOF</span></div>
                                    <p class="text-sm text-slate-400">/mois</p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Tout du plan Pro
                                    </li>
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Multi-utilisateurs
                                    </li>
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        API accès
                                    </li>
                                    <li class="flex items-center text-sm text-slate-600">
                                        <svg class="h-5 w-5 text-accent-500 mr-2.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Gestionnaire dédié
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center mt-10">
                            <button type="button" onclick="goToStep2()"
                                class="btn-primary btn-shine auth-shimmer inline-flex items-center gap-2 bg-gradient-to-r from-brand-600 to-brand-700 text-white px-10 py-4 rounded-2xl font-bold text-base shadow-btn cursor-pointer group">
                                Continuer
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ===== STEP 2: Information ===== --}}
                    <div id="infoStep" class="hidden">
                        <div
                            class="max-w-2xl mx-auto auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl shadow-elevated border border-white/60 p-8 sm:p-10">
                            <h2 class="text-2xl font-bold text-slate-800 text-center mb-8">Vos informations</h2>

                            <div class="auth-stagger space-y-5">
                                {{-- Company Name --}}
                                <div class="auth-reveal">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nom de l'entreprise
                                        *</label>
                                    <div class="auth-input-wrap relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 7.5h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="company_name" required
                                            class="block w-full pl-10 pr-4 py-3.5 bg-surface/80 border border-slate-200 rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all"
                                            placeholder="Ex: Ma Super Entreprise">
                                    </div>
                                </div>

                                {{-- Full Name --}}
                                <div class="auth-reveal">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Votre nom complet
                                        *</label>
                                    <div class="auth-input-wrap relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="name" required
                                            class="block w-full pl-10 pr-4 py-3.5 bg-surface/80 border border-slate-200 rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all"
                                            placeholder="Ex: Jean Dupont">
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="auth-reveal">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email professionnel
                                        *</label>
                                    <div class="auth-input-wrap relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                            </svg>
                                        </div>
                                        <input type="email" name="email" required
                                            class="block w-full pl-10 pr-4 py-3.5 bg-surface/80 border border-slate-200 rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all"
                                            placeholder="jean@entreprise.com">
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="auth-reveal" x-data="{ show: false }">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mot de passe *</label>
                                    <div class="auth-input-wrap relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                        </div>
                                        <input :type="show ? 'text' : 'password'" name="password" required
                                            class="block w-full pl-10 pr-12 py-3.5 bg-surface/80 border border-slate-200 rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all"
                                            placeholder="Min. 8 caractères">
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-brand-600 transition-colors cursor-pointer">
                                            <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <svg x-show="show" x-cloak class="h-4 w-4" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Password Confirmation --}}
                                <div class="auth-reveal" x-data="{ show: false }">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Confirmer le mot de
                                        passe *</label>
                                    <div class="auth-input-wrap relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                        </div>
                                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                                            class="block w-full pl-10 pr-12 py-3.5 bg-surface/80 border border-slate-200 rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all"
                                            placeholder="Répétez le mot de passe">
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-brand-600 transition-colors cursor-pointer">
                                            <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <svg x-show="show" x-cloak class="h-4 w-4" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Terms --}}
                                <div class="auth-reveal flex items-start gap-3">
                                    <input type="checkbox" name="terms" required
                                        class="mt-0.5 h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded cursor-pointer">
                                    <label class="text-sm text-slate-600 leading-snug">
                                        J'accepte les <a href="/conditions-generales"
                                            class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">conditions
                                            générales</a> et la <a href="/politique-confidentialite"
                                            class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">politique
                                            de confidentialité</a>
                                    </label>
                                </div>
                            </div>

                            {{-- Plan Summary --}}
                            <div class="mt-8 p-5 bg-brand-50/50 border border-brand-100 rounded-2xl">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-bold text-slate-800">Plan <span id="selectedPlanName"
                                                class="text-brand-600">Pro</span></div>
                                        <div class="text-xs text-slate-400 mt-0.5">Essai gratuit de 14 jours</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-extrabold auth-gradient-text"><span
                                                id="selectedPlanPrice">19 000</span> <span
                                                class="text-sm font-semibold text-slate-400">XOF</span></div>
                                        <div class="text-xs text-slate-400">/mois après l'essai</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex gap-4 mt-8">
                                <button type="button" onclick="goToStep1()"
                                    class="flex-1 flex justify-center items-center gap-2 py-3.5 px-4 border-2 border-slate-200/80 rounded-2xl text-sm font-semibold text-slate-700 bg-white/80 hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer group">
                                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none"
                                        stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                    </svg>
                                    Retour
                                </button>
                                <button type="submit"
                                    class="btn-primary btn-shine auth-shimmer flex-1 flex justify-center items-center gap-2 py-3.5 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 shadow-btn cursor-pointer group">
                                    Créer mon compte
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                        stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Login link --}}
                <div class="auth-reveal text-center mt-8" style="transition-delay: 300ms">
                    <p class="text-sm text-slate-500">
                        Vous avez déjà un compte ?
                        <a href="{{ route('login') }}"
                            class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Connectez-vous</a>
                    </p>
                </div>

                {{-- Trust badges --}}
                <div class="auth-reveal flex justify-center gap-3 mt-6" style="transition-delay: 400ms">
                    <span
                        class="auth-float-badge flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/70 backdrop-blur-sm border border-slate-100 shadow-sm text-[11px] text-slate-400"
                        style="animation-delay: 0s">
                        <svg class="w-3.5 h-3.5 text-accent-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        SSL 256-bit
                    </span>
                    <span
                        class="auth-float-badge flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/70 backdrop-blur-sm border border-slate-100 shadow-sm text-[11px] text-slate-400"
                        style="animation-delay: .5s">
                        <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        Données protégées
                    </span>
                    <span
                        class="auth-float-badge flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/70 backdrop-blur-sm border border-slate-100 shadow-sm text-[11px] text-slate-400"
                        style="animation-delay: 1s">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        14 jours d'essai
                    </span>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let selectedPlan = 'pro';
        let selectedPrice = 19000;

        function selectPlan(plan, price) {
            // Deselect all
            document.querySelectorAll('.plan-card').forEach(card => {
                card.classList.remove('plan-selected');
                const check = card.querySelector('.plan-check');
                if (check) {
                    check.classList.add('hidden');
                    check.classList.remove('flex');
                }
            });

            // Select clicked
            event.currentTarget.classList.add('plan-selected');
            const check = event.currentTarget.querySelector('.plan-check');
            if (check) {
                check.classList.remove('hidden');
                check.classList.add('flex');
            }
            document.getElementById('plan-' + plan).checked = true;

            selectedPlan = plan;
            selectedPrice = price;

            // Update summary
            document.getElementById('selectedPlanName').textContent = plan.charAt(0).toUpperCase() + plan.slice(1);
            if (price === 0) {
                document.getElementById('selectedPlanPrice').textContent = 'Gratuit';
            } else {
                document.getElementById('selectedPlanPrice').textContent = price.toLocaleString('fr-FR');
            }
        }

        function goToStep2() {
            document.getElementById('planStep').classList.add('hidden');
            document.getElementById('infoStep').classList.remove('hidden');

            // Step indicators
            document.getElementById('step1').classList.remove('bg-brand-600');
            document.getElementById('step1').classList.add('bg-accent-500');
            document.getElementById('step1').innerHTML =
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';

            document.getElementById('step2').classList.remove('bg-slate-200', 'text-slate-500');
            document.getElementById('step2').classList.add('bg-brand-600', 'text-white', 'shadow-btn');
            document.getElementById('step2Label').classList.remove('text-slate-400');
            document.getElementById('step2Label').classList.add('text-slate-800', 'font-semibold');

            document.getElementById('stepLine1').classList.add('active');

            // Re-trigger reveal animations for step 2 fields
            document.querySelectorAll('#infoStep .auth-reveal').forEach(el => {
                el.classList.remove('visible');
                void el.offsetWidth;
                el.classList.add('visible');
            });

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function goToStep1() {
            document.getElementById('infoStep').classList.add('hidden');
            document.getElementById('planStep').classList.remove('hidden');

            // Reset step indicators
            document.getElementById('step1').classList.remove('bg-accent-500');
            document.getElementById('step1').classList.add('bg-brand-600');
            document.getElementById('step1').textContent = '1';

            document.getElementById('step2').classList.remove('bg-brand-600', 'text-white', 'shadow-btn');
            document.getElementById('step2').classList.add('bg-slate-200', 'text-slate-500');
            document.getElementById('step2Label').classList.remove('text-slate-800', 'font-semibold');
            document.getElementById('step2Label').classList.add('text-slate-400');

            document.getElementById('stepLine1').classList.remove('active');

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Auto-select Pro plan on load
        document.addEventListener('DOMContentLoaded', function() {
            const proCard = document.querySelector('.plan-card:nth-child(2)');
            if (proCard) {
                proCard.classList.add('plan-selected');
                const check = proCard.querySelector('.plan-check');
                if (check) {
                    check.classList.remove('hidden');
                    check.classList.add('flex');
                }
            }
        });
    </script>
@endsection
