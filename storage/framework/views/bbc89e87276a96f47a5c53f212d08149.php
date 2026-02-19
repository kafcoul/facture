<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'InvoiceSaaS')); ?> — <?php echo $__env->yieldContent('title', 'Authentification'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b'
                        },
                        accent: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669'
                        },
                        deep: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e3a8a',
                            900: '#1e2a5e'
                        },
                        surface: '#F8FAFC',
                        'surface-2': '#F1F5F9'
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif']
                    },
                    boxShadow: {
                        'card': '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06)',
                        'card-hover': '0 12px 28px rgba(0,0,0,0.08), 0 4px 10px rgba(0,0,0,0.04)',
                        'elevated': '0 20px 60px rgba(0,0,0,0.10), 0 8px 20px rgba(0,0,0,0.06)',
                        'btn': '0 1px 2px rgba(0,0,0,0.05), 0 4px 12px rgba(79,70,229,0.25)',
                    }
                }
            }
        }
    </script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * {
            font-family: 'Inter', system-ui, sans-serif
        }

        [x-cloak] {
            display: none !important;
        }

        .nav-scrolled {
            background: rgba(255, 255, 255, .97);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
            backdrop-filter: blur(12px)
        }

        .btn-primary {
            transition: all .25s cubic-bezier(.16, 1, .3, 1);
            position: relative;
            overflow: hidden
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, .35)
        }

        .btn-primary:active {
            transform: translateY(0)
        }

        .btn-shine {
            position: relative;
            overflow: hidden
        }

        .btn-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .12), transparent);
            transition: left .5s ease
        }

        .btn-shine:hover::before {
            left: 100%
        }

        /* ===== AUTH PAGE BACKGROUNDS ===== */
        .auth-bg {
            background:
                radial-gradient(ellipse 80% 60% at 50% -20%, rgba(30, 58, 138, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 85% 15%, rgba(79, 70, 229, 0.06) 0%, transparent 50%),
                radial-gradient(ellipse 40% 50% at 10% 70%, rgba(16, 185, 129, 0.04) 0%, transparent 50%),
                radial-gradient(ellipse 60% 50% at 50% 100%, rgba(79, 70, 229, 0.03) 0%, transparent 40%);
        }

        .auth-dots {
            background-image: radial-gradient(circle, rgba(30, 58, 138, .04) 1px, transparent 1px);
            background-size: 28px 28px
        }

        /* ===== FLOATING ORBS ===== */
        .auth-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0;
            animation: authOrbFloat 12s ease-in-out infinite, authOrbFadeIn 1.2s ease forwards;
            pointer-events: none
        }

        .auth-orb-1 {
            width: 350px;
            height: 350px;
            background: rgba(79, 70, 229, .10);
            top: -10%;
            right: -5%;
            animation-delay: 0s
        }

        .auth-orb-2 {
            width: 280px;
            height: 280px;
            background: rgba(16, 185, 129, .08);
            bottom: -5%;
            left: -8%;
            animation-delay: 2s
        }

        .auth-orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(99, 102, 241, .07);
            top: 40%;
            left: 60%;
            animation-delay: 4s
        }

        @keyframes authOrbFloat {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            25% {
                transform: translate(15px, -20px) scale(1.05)
            }

            50% {
                transform: translate(-10px, 15px) scale(.95)
            }

            75% {
                transform: translate(20px, 10px) scale(1.03)
            }
        }

        @keyframes authOrbFadeIn {
            to {
                opacity: 1
            }
        }

        /* ===== FLOATING GRID LINES ===== */
        .auth-grid {
            background-image:
                linear-gradient(rgba(79, 70, 229, .03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79, 70, 229, .03) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridSlide 20s linear infinite
        }

        @keyframes gridSlide {
            0% {
                background-position: 0 0
            }

            100% {
                background-position: 60px 60px
            }
        }

        /* ===== REVEAL ANIMATIONS ===== */
        .auth-reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: all .7s cubic-bezier(.16, 1, .3, 1)
        }

        .auth-reveal.visible {
            opacity: 1;
            transform: translateY(0)
        }

        .auth-reveal-scale {
            opacity: 0;
            transform: scale(.95) translateY(16px);
            transition: all .7s cubic-bezier(.16, 1, .3, 1)
        }

        .auth-reveal-scale.visible {
            opacity: 1;
            transform: scale(1) translateY(0)
        }

        .auth-reveal-left {
            opacity: 0;
            transform: translateX(-20px);
            transition: all .6s cubic-bezier(.16, 1, .3, 1)
        }

        .auth-reveal-left.visible {
            opacity: 1;
            transform: translateX(0)
        }

        /* ===== CARD GLOW BORDER ===== */
        .auth-card-glow {
            position: relative;
            transition: all .4s cubic-bezier(.16, 1, .3, 1)
        }

        .auth-card-glow::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 24px;
            padding: 1.5px;
            background: linear-gradient(135deg, transparent 30%, rgba(79, 70, 229, .20) 50%, rgba(16, 185, 129, .12) 70%, transparent 90%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity .5s ease;
            pointer-events: none
        }

        .auth-card-glow:hover::before {
            opacity: 1
        }

        .auth-card-glow:hover {
            box-shadow: 0 24px 64px rgba(79, 70, 229, .08), 0 8px 24px rgba(0, 0, 0, .04)
        }

        /* ===== INPUT FOCUS ANIMATION ===== */
        .auth-input-wrap {
            position: relative
        }

        .auth-input-wrap::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #10b981);
            border-radius: 2px;
            transition: all .4s cubic-bezier(.16, 1, .3, 1);
            transform: translateX(-50%)
        }

        .auth-input-wrap:focus-within::after {
            width: 100%
        }

        .auth-input-wrap .auth-icon {
            transition: all .3s ease
        }

        .auth-input-wrap:focus-within .auth-icon {
            color: #6366f1;
            transform: scale(1.1)
        }

        /* ===== STAGGER CHILDREN ===== */
        .auth-stagger>*:nth-child(1) {
            transition-delay: 0ms
        }

        .auth-stagger>*:nth-child(2) {
            transition-delay: 80ms
        }

        .auth-stagger>*:nth-child(3) {
            transition-delay: 160ms
        }

        .auth-stagger>*:nth-child(4) {
            transition-delay: 240ms
        }

        .auth-stagger>*:nth-child(5) {
            transition-delay: 320ms
        }

        .auth-stagger>*:nth-child(6) {
            transition-delay: 400ms
        }

        .auth-stagger>*:nth-child(7) {
            transition-delay: 480ms
        }

        /* ===== FLOATING PARTICLES ===== */
        .auth-particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            opacity: 0;
            animation: authParticleFloat linear infinite, authOrbFadeIn .8s ease forwards
        }

        @keyframes authParticleFloat {
            0% {
                transform: translateY(0) rotate(0deg)
            }

            100% {
                transform: translateY(-100vh) rotate(360deg)
            }
        }

        /* ===== ICON PULSE ===== */
        .auth-icon-pulse {
            animation: iconPulse 3s ease-in-out infinite
        }

        @keyframes iconPulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(79, 70, 229, .2)
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 12px rgba(79, 70, 229, 0)
            }
        }

        /* ===== GRADIENT TEXT ===== */
        .auth-gradient-text {
            background: linear-gradient(135deg, #1e2a5e 0%, #4f46e5 50%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text
        }

        /* ===== SHIMMER LOADING ===== */
        .auth-shimmer {
            position: relative;
            overflow: hidden
        }

        .auth-shimmer::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .4), transparent);
            animation: authShimmer 4s ease-in-out 1s infinite
        }

        @keyframes authShimmer {
            0% {
                left: -100%
            }

            100% {
                left: 200%
            }
        }

        /* ===== SPINNER ===== */
        .auth-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, .3);
            border-top-color: white;
            border-radius: 50%;
            animation: authSpin .6s linear infinite
        }

        @keyframes authSpin {
            to {
                transform: rotate(360deg)
            }
        }

        /* ===== MESH GRADIENT BACKGROUND ===== */
        .auth-mesh {
            background:
                conic-gradient(from 230deg at 51% 52%, rgba(79, 70, 229, .06) 0deg, rgba(16, 185, 129, .04) 67.5deg, rgba(99, 102, 241, .05) 198.75deg, rgba(52, 211, 153, .03) 251.25deg, rgba(79, 70, 229, .06) 301.88deg, transparent 360deg);
            animation: meshRotate 25s linear infinite;
        }

        @keyframes meshRotate {
            0% {
                filter: hue-rotate(0deg)
            }

            100% {
                filter: hue-rotate(360deg)
            }
        }

        /* ===== AURORA EFFECT ===== */
        .auth-aurora {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none
        }

        .auth-aurora::before,
        .auth-aurora::after {
            content: '';
            position: absolute;
            width: 60%;
            height: 60%;
            border-radius: 50%;
            filter: blur(100px);
            opacity: .35;
            animation: auroraMove 15s ease-in-out infinite alternate
        }

        .auth-aurora::before {
            background: linear-gradient(135deg, rgba(79, 70, 229, .15), rgba(99, 102, 241, .10));
            top: -20%;
            left: -10%;
            animation-delay: 0s
        }

        .auth-aurora::after {
            background: linear-gradient(135deg, rgba(16, 185, 129, .10), rgba(52, 211, 153, .08));
            bottom: -20%;
            right: -10%;
            animation-delay: -7.5s
        }

        @keyframes auroraMove {
            0% {
                transform: translate(0, 0) scale(1) rotate(0deg)
            }

            33% {
                transform: translate(30px, -20px) scale(1.1) rotate(5deg)
            }

            66% {
                transform: translate(-20px, 30px) scale(.9) rotate(-3deg)
            }

            100% {
                transform: translate(10px, -10px) scale(1.05) rotate(2deg)
            }
        }

        /* ===== MORPHING BLOBS ===== */
        .auth-blob {
            position: absolute;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            filter: blur(50px);
            opacity: 0;
            animation: blobMorph 20s ease-in-out infinite, authOrbFadeIn 1.5s ease forwards;
            pointer-events: none;
        }

        .auth-blob-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, rgba(79, 70, 229, .08), rgba(99, 102, 241, .05));
            top: -5%;
            right: 10%;
            animation-delay: 0s;
        }

        .auth-blob-2 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(16, 185, 129, .06), rgba(52, 211, 153, .04));
            bottom: 5%;
            left: 5%;
            animation-delay: 5s;
        }

        .auth-blob-3 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, rgba(245, 158, 11, .05), rgba(251, 191, 36, .03));
            top: 50%;
            left: 50%;
            animation-delay: 10s;
        }

        @keyframes blobMorph {

            0%,
            100% {
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                transform: rotate(0deg) scale(1)
            }

            25% {
                border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%;
                transform: rotate(90deg) scale(1.05)
            }

            50% {
                border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%;
                transform: rotate(180deg) scale(.95)
            }

            75% {
                border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%;
                transform: rotate(270deg) scale(1.03)
            }
        }

        /* ===== SPLIT PANEL LEFT SIDE ===== */
        .auth-panel-left {
            background: linear-gradient(160deg, #1e1b4b 0%, #312e81 30%, #3730a3 60%, #1e3a8a 100%);
            position: relative;
            overflow: hidden;
        }

        .auth-panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, .3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(16, 185, 129, .2) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(139, 92, 246, .15) 0%, transparent 50%);
            animation: panelGlow 12s ease-in-out infinite alternate;
        }

        @keyframes panelGlow {
            0% {
                opacity: .6
            }

            50% {
                opacity: 1
            }

            100% {
                opacity: .7
            }
        }

        /* Panel floating shapes */
        .panel-shape {
            position: absolute;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 16px;
            animation: panelShapeFloat 18s ease-in-out infinite;
            pointer-events: none;
        }

        .panel-shape-1 {
            width: 120px;
            height: 120px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
            transform: rotate(15deg)
        }

        .panel-shape-2 {
            width: 80px;
            height: 80px;
            bottom: 15%;
            right: 8%;
            animation-delay: 3s;
            transform: rotate(-20deg)
        }

        .panel-shape-3 {
            width: 60px;
            height: 60px;
            top: 55%;
            left: 60%;
            animation-delay: 6s;
            transform: rotate(45deg)
        }

        .panel-shape-4 {
            width: 100px;
            height: 100px;
            top: 70%;
            left: 15%;
            animation-delay: 9s;
            transform: rotate(-10deg);
            border-radius: 50%
        }

        @keyframes panelShapeFloat {

            0%,
            100% {
                transform: translateY(0) rotate(var(--r, 15deg))
            }

            50% {
                transform: translateY(-15px) rotate(calc(var(--r, 15deg) + 10deg))
            }
        }

        /* Panel animated line */
        .panel-line {
            position: absolute;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .1), transparent);
            animation: panelLineSweep 8s ease-in-out infinite;
        }

        @keyframes panelLineSweep {

            0%,
            100% {
                transform: translateY(0);
                opacity: 0
            }

            50% {
                opacity: 1
            }
        }

        .panel-line-1 {
            top: 25%;
            animation-delay: 0s
        }

        .panel-line-2 {
            top: 55%;
            animation-delay: 2s
        }

        .panel-line-3 {
            top: 80%;
            animation-delay: 4s
        }

        /* ===== ANIMATED STAT COUNTER ===== */
        .stat-counter {
            animation: statPop .6s cubic-bezier(.16, 1, .3, 1) both;
        }

        @keyframes statPop {
            0% {
                transform: scale(.8);
                opacity: 0
            }

            100% {
                transform: scale(1);
                opacity: 1
            }
        }

        /* ===== TYPING ANIMATION ===== */
        .auth-typing::after {
            content: '|';
            animation: typingBlink 1s step-end infinite;
            color: #6366f1;
            font-weight: 300;
        }

        @keyframes typingBlink {

            0%,
            50% {
                opacity: 1
            }

            51%,
            100% {
                opacity: 0
            }
        }

        /* ===== FLOATING BADGE ANIMATION ===== */
        .auth-float-badge {
            animation: floatBadge 6s ease-in-out infinite;
        }

        @keyframes floatBadge {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-6px)
            }
        }

        /* ===== ANIMATED RING ===== */
        .auth-ring {
            position: absolute;
            border: 1.5px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
            animation: ringExpand 8s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes ringExpand {

            0%,
            100% {
                transform: scale(.8);
                opacity: 0
            }

            50% {
                transform: scale(1.2);
                opacity: 1
            }
        }

        /* ===== SUCCESS CHECK ANIMATION ===== */
        .auth-check-anim {
            stroke-dasharray: 24;
            stroke-dashoffset: 24;
            animation: checkDraw .5s ease forwards;
        }

        @keyframes checkDraw {
            to {
                stroke-dashoffset: 0
            }
        }

        /* ===== PULSE DOT ===== */
        .auth-pulse-dot {
            position: relative;
        }

        .auth-pulse-dot::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: inherit;
            transform: translate(-50%, -50%);
            animation: pulseDot 2s ease-in-out infinite;
        }

        @keyframes pulseDot {

            0%,
            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: .5
            }

            50% {
                transform: translate(-50%, -50%) scale(2);
                opacity: 0
            }
        }

        /* ===== REVEAL FROM RIGHT ===== */
        .auth-reveal-right {
            opacity: 0;
            transform: translateX(20px);
            transition: all .6s cubic-bezier(.16, 1, .3, 1)
        }

        .auth-reveal-right.visible {
            opacity: 1;
            transform: translateX(0)
        }

        /* ===== SLIDE UP STAGGER (panel items) ===== */
        .panel-stagger>*:nth-child(1) {
            transition-delay: 300ms
        }

        .panel-stagger>*:nth-child(2) {
            transition-delay: 450ms
        }

        .panel-stagger>*:nth-child(3) {
            transition-delay: 600ms
        }

        .panel-stagger>*:nth-child(4) {
            transition-delay: 750ms
        }

        .panel-stagger>*:nth-child(5) {
            transition-delay: 900ms
        }

        .panel-stagger>*:nth-child(6) {
            transition-delay: 1050ms
        }
    </style>
    <?php echo $__env->yieldContent('styles'); ?>
</head>

<body class="bg-surface text-slate-900 antialiased min-h-screen flex flex-col">

    
    <nav id="mainNav"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/80 backdrop-blur-lg border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-extrabold tracking-tight text-slate-900">Invoice<span
                    class="text-brand-600">SaaS</span></a>
            <div class="hidden md:flex items-center gap-8">
                <a href="/"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Accueil</a>
                <a href="/#fonctionnalites"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Fonctionnalités</a>
                <a href="/#tarifs"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Tarifs</a>
                <a href="/#faq"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">FAQ</a>
            </div>
            <div class="flex items-center gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('login')): ?>
                    <a href="<?php echo e(route('register')); ?>"
                        class="btn-primary btn-shine bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-btn cursor-pointer">
                        Créer un compte
                    </a>
                <?php elseif(request()->routeIs('register')): ?>
                    <a href="<?php echo e(route('login')); ?>"
                        class="btn-primary btn-shine bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-btn cursor-pointer">
                        Connexion
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>"
                        class="hidden sm:inline-flex text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors px-4 py-2">
                        Connexion
                    </a>
                    <a href="<?php echo e(route('register')); ?>"
                        class="btn-primary btn-shine bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-btn cursor-pointer">
                        Essai gratuit
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </nav>

    
    <main class="flex-1 pt-[72px]">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <footer class="py-14 bg-deep-900 border-t border-deep-800">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <div class="grid md:grid-cols-4 gap-10">
                <div class="md:col-span-1">
                    <a href="/" class="text-xl font-extrabold text-white">Invoice<span
                            class="text-brand-400">SaaS</span></a>
                    <p class="text-sm text-slate-400 mt-3 leading-relaxed">La plateforme de facturation pensée pour les
                        entrepreneurs africains.</p>
                    <div class="flex gap-3 mt-5">
                        <a href="#"
                            class="w-8 h-8 rounded-lg bg-deep-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-8 h-8 rounded-lg bg-deep-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4">Produit</h4>
                    <ul class="space-y-2.5">
                        <li><a href="/#fonctionnalites"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Fonctionnalités</a>
                        </li>
                        <li><a href="/#tarifs"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Tarifs</a></li>
                        <li><a href="/#temoignages"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Témoignages</a></li>
                        <li><a href="/#faq" class="text-sm text-slate-400 hover:text-white transition-colors">FAQ</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4">Légal</h4>
                    <ul class="space-y-2.5">
                        <li><a href="/conditions-generales"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Conditions
                                générales</a></li>
                        <li><a href="/politique-confidentialite"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Politique de
                                confidentialité</a></li>
                        <li><a href="/mentions-legales"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Mentions légales</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4">Contact</h4>
                    <ul class="space-y-2.5">
                        <li><a href="mailto:contact@invoicesaas.com"
                                class="text-sm text-slate-400 hover:text-white transition-colors">contact@invoicesaas.com</a>
                        </li>
                        <li><span class="text-sm text-slate-400">Dakar, Sénégal</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-deep-800 mt-10 pt-6 text-center">
                <p class="text-xs text-slate-500">&copy; <?php echo e(date('Y')); ?> InvoiceSaaS. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Nav scroll effect
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                nav.classList.add('nav-scrolled');
                nav.classList.remove('bg-white/80', 'backdrop-blur-lg', 'border-b', 'border-slate-100');
            } else {
                nav.classList.remove('nav-scrolled');
                nav.classList.add('bg-white/80', 'backdrop-blur-lg', 'border-b', 'border-slate-100');
            }
        }, {
            passive: true
        });

        // Scroll reveal animations
        document.addEventListener('DOMContentLoaded', function() {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -30px 0px'
            });

            document.querySelectorAll('.auth-reveal, .auth-reveal-scale, .auth-reveal-left, .auth-reveal-right')
                .forEach(el => obs.observe(el));

            // Enhanced particle system
            const particleContainer = document.getElementById('authParticles');
            if (particleContainer) {
                const colors = ['rgba(79,70,229,.12)', 'rgba(16,185,129,.10)', 'rgba(99,102,241,.08)',
                    'rgba(52,211,153,.08)', 'rgba(139,92,246,.08)'
                ];
                for (let i = 0; i < 20; i++) {
                    const p = document.createElement('div');
                    p.className = 'auth-particle';
                    const size = 2 + Math.random() * 6;
                    p.style.width = size + 'px';
                    p.style.height = size + 'px';
                    p.style.background = colors[Math.floor(Math.random() * colors.length)];
                    p.style.left = Math.random() * 100 + '%';
                    p.style.top = (70 + Math.random() * 50) + '%';
                    p.style.animationDuration = (10 + Math.random() * 25) + 's';
                    p.style.animationDelay = (Math.random() * 12) + 's';
                    particleContainer.appendChild(p);
                }
            }

            // Mouse-follow parallax for blobs/orbs
            const blobs = document.querySelectorAll('.auth-blob, .auth-orb');
            if (blobs.length > 0) {
                document.addEventListener('mousemove', function(e) {
                    const cx = (e.clientX / window.innerWidth - 0.5) * 2;
                    const cy = (e.clientY / window.innerHeight - 0.5) * 2;
                    blobs.forEach((blob, i) => {
                        const factor = (i + 1) * 8;
                        blob.style.transform = `translate(${cx * factor}px, ${cy * factor}px)`;
                    });
                }, {
                    passive: true
                });
            }

            // Animated counter
            document.querySelectorAll('[data-count]').forEach(el => {
                const target = parseInt(el.dataset.count, 10);
                const suffix = el.dataset.suffix || '';
                const duration = 2000;
                const start = performance.now();

                function tick(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const ease = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(target * ease) + suffix;
                    if (progress < 1) requestAnimationFrame(tick);
                }
                const cObs = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        requestAnimationFrame(tick);
                        cObs.disconnect();
                    }
                }, {
                    threshold: 0.5
                });
                cObs.observe(el);
            });

            // Typing placeholder animation
            const typingInputs = document.querySelectorAll('[data-typing]');
            typingInputs.forEach(input => {
                const phrases = input.dataset.typing.split('|');
                let pIdx = 0,
                    cIdx = 0,
                    deleting = false;

                function typeStep() {
                    const phrase = phrases[pIdx];
                    if (!deleting) {
                        input.setAttribute('placeholder', phrase.substring(0, cIdx + 1));
                        cIdx++;
                        if (cIdx >= phrase.length) {
                            deleting = true;
                            setTimeout(typeStep, 2000);
                            return;
                        }
                        setTimeout(typeStep, 80 + Math.random() * 40);
                    } else {
                        input.setAttribute('placeholder', phrase.substring(0, cIdx));
                        cIdx--;
                        if (cIdx < 0) {
                            deleting = false;
                            pIdx = (pIdx + 1) % phrases.length;
                            cIdx = 0;
                            setTimeout(typeStep, 400);
                            return;
                        }
                        setTimeout(typeStep, 40);
                    }
                }
                setTimeout(typeStep, 1500);
            });

            // Panel star particles
            const panelParticles = document.getElementById('panelParticles');
            if (panelParticles) {
                for (let i = 0; i < 30; i++) {
                    const star = document.createElement('div');
                    const s = 1 + Math.random() * 2;
                    star.style.cssText =
                        `position:absolute;width:${s}px;height:${s}px;background:rgba(255,255,255,${.1+Math.random()*.3});border-radius:50%;left:${Math.random()*100}%;top:${Math.random()*100}%;animation:panelStarTwinkle ${2+Math.random()*4}s ease-in-out ${Math.random()*3}s infinite alternate`;
                    panelParticles.appendChild(star);
                }
                // inject twinkle keyframe
                const style = document.createElement('style');
                style.textContent =
                    '@keyframes panelStarTwinkle { 0% { opacity: .1; transform: scale(.5) } 100% { opacity: 1; transform: scale(1.2) } }';
                document.head.appendChild(style);
            }
        });
    </script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html>
<?php /**PATH /Users/teya2023/Downloads/invoice-saas-starter/resources/views/layouts/guest.blade.php ENDPATH**/ ?>