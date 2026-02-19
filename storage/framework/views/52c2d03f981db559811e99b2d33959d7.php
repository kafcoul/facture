<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="InvoiceSaaS — Facturez en 30 secondes, encaissez instantanément par Mobile Money ou carte. La plateforme de facturation faite pour l'Afrique.">
    <meta name="keywords"
        content="facturation, SaaS, Afrique, Mobile Money, Wave, Orange Money, facture, paiement, PME, freelance">
    <title>InvoiceSaaS — Facturez en 30 secondes. Soyez payé instantanément.</title>
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
                    borderRadius: {
                        '3xl': '24px',
                        '2xl': '16px',
                        'xl': '12px'
                    },
                    boxShadow: {
                        'card': '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06)',
                        'card-hover': '0 12px 28px rgba(0,0,0,0.08), 0 4px 10px rgba(0,0,0,0.04)',
                        'elevated': '0 20px 60px rgba(0,0,0,0.10), 0 8px 20px rgba(0,0,0,0.06)',
                        'glow': '0 0 50px rgba(79,70,229,0.12)',
                        'glow-accent': '0 0 50px rgba(16,185,129,0.12)',
                        'inner-glow': 'inset 0 1px 0 rgba(255,255,255,0.1)',
                        'btn': '0 1px 2px rgba(0,0,0,0.05), 0 4px 12px rgba(79,70,229,0.25)',
                        'btn-hover': '0 2px 4px rgba(0,0,0,0.05), 0 8px 20px rgba(79,70,229,0.35)'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', system-ui, sans-serif
        }

        /* ===== SCROLL ANIMATIONS ===== */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: all .7s cubic-bezier(.16, 1, .3, 1)
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0)
        }

        .reveal-left {
            opacity: 0;
            transform: translateX(-28px);
            transition: all .7s cubic-bezier(.16, 1, .3, 1)
        }

        .reveal-left.visible {
            opacity: 1;
            transform: translateX(0)
        }

        .reveal-right {
            opacity: 0;
            transform: translateX(28px);
            transition: all .7s cubic-bezier(.16, 1, .3, 1)
        }

        .reveal-right.visible {
            opacity: 1;
            transform: translateX(0)
        }

        .reveal-scale {
            opacity: 0;
            transform: scale(.96);
            transition: all .7s cubic-bezier(.16, 1, .3, 1)
        }

        .reveal-scale.visible {
            opacity: 1;
            transform: scale(1)
        }

        /* ===== STAGGER ANIMATION ===== */
        .stagger-grid>* {
            opacity: 0;
            transform: translateY(24px) scale(.97);
            transition: all .6s cubic-bezier(.16, 1, .3, 1)
        }

        .stagger-grid.visible>* {
            opacity: 1;
            transform: translateY(0) scale(1)
        }

        .stagger-grid.visible>*:nth-child(1) {
            transition-delay: 0ms
        }

        .stagger-grid.visible>*:nth-child(2) {
            transition-delay: 80ms
        }

        .stagger-grid.visible>*:nth-child(3) {
            transition-delay: 160ms
        }

        .stagger-grid.visible>*:nth-child(4) {
            transition-delay: 240ms
        }

        .stagger-grid.visible>*:nth-child(5) {
            transition-delay: 320ms
        }

        .stagger-grid.visible>*:nth-child(6) {
            transition-delay: 400ms
        }

        .stagger-grid.visible>*:nth-child(7) {
            transition-delay: 480ms
        }

        .stagger-grid.visible>*:nth-child(8) {
            transition-delay: 560ms
        }

        /* ===== HERO ===== */
        .hero-gradient {
            background:
                radial-gradient(ellipse 80% 60% at 50% -20%, rgba(30, 58, 138, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 20%, rgba(79, 70, 229, 0.06) 0%, transparent 50%),
                radial-gradient(ellipse 40% 50% at 15% 60%, rgba(16, 185, 129, 0.04) 0%, transparent 50%);
        }

        .hero-dots {
            background-image: radial-gradient(circle, rgba(30, 58, 138, .06) 1px, transparent 1px);
            background-size: 28px 28px
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0;
            animation: orbFloat 10s ease-in-out infinite, orbFadeIn 1.5s ease forwards
        }

        .hero-orb-1 {
            width: 350px;
            height: 350px;
            background: rgba(30, 58, 138, .10);
            top: -8%;
            right: 8%;
            animation-delay: 0s
        }

        .hero-orb-2 {
            width: 250px;
            height: 250px;
            background: rgba(16, 185, 129, .08);
            bottom: 5%;
            left: 3%;
            animation-delay: 3s
        }

        .hero-orb-3 {
            width: 180px;
            height: 180px;
            background: rgba(79, 70, 229, .06);
            top: 35%;
            right: -3%;
            animation-delay: 6s
        }

        @keyframes orbFloat {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            25% {
                transform: translate(12px, -18px) scale(1.04)
            }

            50% {
                transform: translate(-8px, 12px) scale(.96)
            }

            75% {
                transform: translate(16px, 8px) scale(1.02)
            }
        }

        @keyframes orbFadeIn {
            to {
                opacity: 1
            }
        }

        /* Dashboard mockup */
        .mockup-3d {
            perspective: 1200px
        }

        .mockup-float {
            animation: mockupFloat 6s ease-in-out infinite;
            transform-style: preserve-3d
        }

        @keyframes mockupFloat {

            0%,
            100% {
                transform: translateY(0) rotateY(-3deg) rotateX(2deg)
            }

            50% {
                transform: translateY(-10px) rotateY(-3deg) rotateX(2deg)
            }
        }

        .mockup-3d:hover .mockup-float {
            animation: none;
            transform: rotateY(0) rotateX(0);
            transition: transform .6s cubic-bezier(.16, 1, .3, 1)
        }

        .mockup-shine {
            position: relative;
            overflow: hidden
        }

        .mockup-shine::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .25), transparent);
            animation: shimmer 5s ease-in-out 2s infinite
        }

        @keyframes shimmer {
            0% {
                left: -100%
            }

            100% {
                left: 200%
            }
        }

        .hero-badge {
            background: rgba(255, 255, 255, .75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(79, 70, 229, .12)
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

        /* ===== CARDS ===== */
        .feature-card {
            transition: all .4s cubic-bezier(.16, 1, .3, 1);
            position: relative
        }

        .feature-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            padding: 1.5px;
            background: linear-gradient(135deg, transparent 40%, rgba(79, 70, 229, .25), rgba(16, 185, 129, .15));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity .4s ease
        }

        .feature-card:hover::before {
            opacity: 1
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(79, 70, 229, .07), 0 4px 12px rgba(0, 0, 0, .03)
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-5deg)
        }

        .feature-icon {
            transition: all .4s cubic-bezier(.16, 1, .3, 1)
        }

        .feature-tag {
            transition: all .3s ease
        }

        .feature-card:hover .feature-tag {
            transform: translateX(2px)
        }

        /* Steps */
        .step-card {
            position: relative;
            opacity: 0;
            transform: translateY(28px);
            transition: all .7s cubic-bezier(.16, 1, .3, 1)
        }

        .steps-container.visible .step-card {
            opacity: 1;
            transform: translateY(0)
        }

        .steps-container.visible .step-card:nth-child(1) {
            transition-delay: 0ms
        }

        .steps-container.visible .step-card:nth-child(2) {
            transition-delay: 180ms
        }

        .steps-container.visible .step-card:nth-child(3) {
            transition-delay: 360ms
        }

        .step-card:hover {
            transform: translateY(-4px)
        }

        .step-card:hover .step-num {
            transform: scale(1.1)
        }

        .step-card:hover .step-icon-wrap {
            transform: rotate(-5deg) scale(1.05)
        }

        .step-num,
        .step-icon-wrap {
            transition: all .4s cubic-bezier(.16, 1, .3, 1)
        }

        .step-progress-line {
            height: 2px;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 1.2s cubic-bezier(.16, 1, .3, 1)
        }

        .steps-container.visible .step-progress-line {
            transform: scaleX(1)
        }

        /* Problem/Solution */
        .ps-card {
            transition: all .4s cubic-bezier(.16, 1, .3, 1)
        }

        .ps-card:hover {
            transform: translateY(-6px)
        }

        .ps-card .ps-icon {
            transition: all .4s cubic-bezier(.16, 1, .3, 1)
        }

        .ps-card:hover .ps-icon {
            transform: scale(1.1) rotate(-5deg)
        }

        .ps-item {
            opacity: 0;
            transform: translateX(-12px);
            transition: all .5s cubic-bezier(.16, 1, .3, 1)
        }

        .ps-card.visible .ps-item {
            opacity: 1;
            transform: translateX(0)
        }

        .ps-card.visible .ps-item:nth-child(1) {
            transition-delay: .1s
        }

        .ps-card.visible .ps-item:nth-child(2) {
            transition-delay: .25s
        }

        .ps-card.visible .ps-item:nth-child(3) {
            transition-delay: .4s
        }

        .ps-item-right {
            opacity: 0;
            transform: translateX(12px);
            transition: all .5s cubic-bezier(.16, 1, .3, 1)
        }

        .ps-card.visible .ps-item-right {
            opacity: 1;
            transform: translateX(0)
        }

        .ps-card.visible .ps-item-right:nth-child(1) {
            transition-delay: .15s
        }

        .ps-card.visible .ps-item-right:nth-child(2) {
            transition-delay: .3s
        }

        .ps-card.visible .ps-item-right:nth-child(3) {
            transition-delay: .45s
        }

        .accent-line {
            width: 0;
            transition: width .8s cubic-bezier(.16, 1, .3, 1) .5s
        }

        .ps-card.visible .accent-line {
            width: 64px
        }

        .num-badge {
            transition: all .3s ease
        }

        .ps-item:hover .num-badge {
            transform: scale(1.15);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, .1)
        }

        .ps-item-right:hover .num-badge {
            transform: scale(1.15);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, .1)
        }

        /* Buttons */
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

        .btn-ghost {
            transition: all .25s cubic-bezier(.16, 1, .3, 1)
        }

        .btn-ghost:hover {
            transform: translateY(-1px)
        }

        .btn-ghost:active {
            transform: translateY(0)
        }

        /* Pricing */
        .pricing-card {
            transition: all .35s cubic-bezier(.16, 1, .3, 1)
        }

        .pricing-card:hover {
            transform: translateY(-6px)
        }

        .pricing-popular {
            box-shadow: 0 0 0 2px #4f46e5, 0 24px 64px rgba(79, 70, 229, .14)
        }

        /* Testimonials */
        .testimonial-card {
            transition: all .35s cubic-bezier(.16, 1, .3, 1)
        }

        .testimonial-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .07)
        }

        /* FAQ */
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height .45s cubic-bezier(.16, 1, .3, 1), padding .35s ease
        }

        .faq-answer.open {
            max-height: 300px
        }

        .faq-chevron {
            transition: transform .35s cubic-bezier(.16, 1, .3, 1)
        }

        .faq-chevron.open {
            transform: rotate(180deg)
        }

        .faq-item {
            transition: all .3s cubic-bezier(.16, 1, .3, 1)
        }

        .faq-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, .04)
        }

        .faq-item.faq-active {
            border-color: rgba(79, 70, 229, .2);
            box-shadow: 0 8px 30px rgba(79, 70, 229, .06)
        }

        .faq-item .faq-num {
            transition: all .35s cubic-bezier(.16, 1, .3, 1)
        }

        .faq-item.faq-active .faq-num {
            background: linear-gradient(135deg, #4f46e5, #10b981);
            color: white;
            transform: scale(1.05)
        }

        /* Counter / Payment pills */
        .counter-card {
            transition: all .3s cubic-bezier(.16, 1, .3, 1)
        }

        .counter-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .06)
        }

        .payment-pill {
            transition: all .3s ease;
            border: 1px solid transparent
        }

        .payment-pill:hover {
            border-color: rgba(79, 70, 229, .15);
            background: rgba(79, 70, 229, .03);
            box-shadow: 0 2px 8px rgba(79, 70, 229, .06)
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* Trust bar */
        .trust-badge {
            transition: all .3s ease;
            opacity: .6
        }

        .trust-badge:hover {
            opacity: 1
        }

        /* Misc */
        .sticky-cta {
            transform: translateY(100%);
            transition: transform .3s ease
        }

        .sticky-cta.visible {
            transform: translateY(0)
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, .3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin .6s linear infinite
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        .nav-scrolled {
            background: rgba(255, 255, 255, .97);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
            backdrop-filter: blur(12px)
        }

        /* Smooth scroll indicator */
        .scroll-indicator {
            animation: scrollBounce 2s ease-in-out infinite
        }

        @keyframes scrollBounce {

            0%,
            100% {
                transform: translateY(0);
                opacity: .6
            }

            50% {
                transform: translateY(6px);
                opacity: 1
            }
        }

        /* ===== MODAL ANIMATIONS ===== */
        .modal-overlay {
            opacity: 0;
            transition: opacity .4s cubic-bezier(.16, 1, .3, 1)
        }

        .modal-overlay.active {
            opacity: 1
        }

        .modal-content {
            opacity: 0;
            transform: translateY(40px) scale(.92);
            transition: all .5s cubic-bezier(.16, 1, .3, 1)
        }

        .modal-content.active {
            opacity: 1;
            transform: translateY(0) scale(1)
        }

        .modal-content.closing {
            opacity: 0;
            transform: translateY(20px) scale(.95);
            transition: all .3s cubic-bezier(.5, 0, .75, 0)
        }

        /* Modal gradient header */
        .modal-header-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 40%, #818cf8 100%);
            position: relative;
            overflow: hidden
        }

        .modal-header-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            animation: modalOrb1 8s ease-in-out infinite
        }

        .modal-header-gradient::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -20%;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .06);
            animation: modalOrb2 10s ease-in-out infinite
        }

        @keyframes modalOrb1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            50% {
                transform: translate(-20px, 15px) scale(1.1)
            }
        }

        @keyframes modalOrb2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            50% {
                transform: translate(15px, -10px) scale(1.15)
            }
        }

        /* Floating particles in header */
        .modal-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, .3);
            border-radius: 50%;
            animation: particleFloat 4s ease-in-out infinite
        }

        .modal-particle:nth-child(1) {
            top: 20%;
            left: 15%;
            animation-delay: 0s;
            width: 3px;
            height: 3px
        }

        .modal-particle:nth-child(2) {
            top: 60%;
            left: 75%;
            animation-delay: 1s;
            width: 5px;
            height: 5px
        }

        .modal-particle:nth-child(3) {
            top: 30%;
            left: 55%;
            animation-delay: 2s
        }

        .modal-particle:nth-child(4) {
            top: 70%;
            left: 30%;
            animation-delay: 0.5s;
            width: 6px;
            height: 6px
        }

        .modal-particle:nth-child(5) {
            top: 15%;
            left: 85%;
            animation-delay: 1.5s;
            width: 3px;
            height: 3px
        }

        @keyframes particleFloat {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
                opacity: .3
            }

            50% {
                transform: translateY(-12px) rotate(180deg);
                opacity: .7
            }
        }

        /* Plan selector enhanced */
        .modal-plan-btn {
            position: relative;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all .35s cubic-bezier(.16, 1, .3, 1);
            background: white;
            overflow: hidden
        }

        .modal-plan-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(79, 70, 229, .04), rgba(99, 102, 241, .08));
            opacity: 0;
            transition: opacity .35s ease;
            border-radius: 14px
        }

        .modal-plan-btn:hover {
            border-color: #c7d2fe;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, .08)
        }

        .modal-plan-btn:hover::before {
            opacity: 1
        }

        .modal-plan-btn.selected {
            border-color: #6366f1;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(79, 70, 229, .15)
        }

        .modal-plan-btn.selected .plan-check {
            opacity: 1;
            transform: scale(1)
        }

        .plan-check {
            opacity: 0;
            transform: scale(0);
            transition: all .3s cubic-bezier(.16, 1, .3, 1)
        }

        .plan-icon-wrap {
            transition: all .35s cubic-bezier(.16, 1, .3, 1)
        }

        .modal-plan-btn.selected .plan-icon-wrap {
            transform: scale(1.1) rotate(-5deg)
        }

        .modal-plan-btn:hover .plan-icon-wrap {
            transform: scale(1.05)
        }

        /* Form fields animation */
        .modal-field {
            opacity: 0;
            transform: translateY(12px);
            animation: fieldAppear .5s cubic-bezier(.16, 1, .3, 1) forwards
        }

        .modal-field:nth-child(1) {
            animation-delay: .15s
        }

        .modal-field:nth-child(2) {
            animation-delay: .25s
        }

        .modal-field:nth-child(3) {
            animation-delay: .35s
        }

        .modal-field:nth-child(4) {
            animation-delay: .45s
        }

        @keyframes fieldAppear {
            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* Input enhanced focus */
        .modal-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px 16px 11px 44px;
            font-size: 14px;
            width: 100%;
            transition: all .3s cubic-bezier(.16, 1, .3, 1);
            background: #f8fafc;
            outline: none
        }

        .modal-input:focus {
            border-color: #6366f1;
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, .1), 0 2px 8px rgba(99, 102, 241, .06)
        }

        .modal-input:hover:not(:focus) {
            border-color: #c7d2fe;
            background: white
        }

        .modal-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color .3s ease;
            pointer-events: none
        }

        .modal-field:focus-within .modal-input-icon {
            color: #6366f1
        }

        /* Password strength */
        .strength-bar {
            height: 3px;
            border-radius: 2px;
            transition: all .4s cubic-bezier(.16, 1, .3, 1);
            transform-origin: left
        }

        /* Submit button pulse */
        .modal-submit {
            position: relative;
            overflow: hidden;
            transition: all .3s cubic-bezier(.16, 1, .3, 1)
        }

        .modal-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .15), transparent);
            transition: left .5s ease
        }

        .modal-submit:hover::before {
            left: 100%
        }

        .modal-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, .35)
        }

        .modal-submit:active {
            transform: translateY(0)
        }

        /* Modal trust badges */
        .modal-trust {
            opacity: 0;
            animation: trustAppear .5s cubic-bezier(.16, 1, .3, 1) .6s forwards
        }

        @keyframes trustAppear {
            to {
                opacity: 1
            }
        }

        /* Confetti burst on open */
        @keyframes confettiBurst {
            0% {
                transform: translate(0, 0) rotate(0deg);
                opacity: 1
            }

            100% {
                transform: translate(var(--tx), var(--ty)) rotate(var(--tr));
                opacity: 0
            }
        }

        .confetti-piece {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 1px;
            animation: confettiBurst .8s cubic-bezier(.25, .46, .45, .94) forwards;
            pointer-events: none
        }
    </style>
</head>

<body class="bg-surface text-slate-900 antialiased">

    
    <nav id="mainNav" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-extrabold tracking-tight text-slate-900">Invoice<span
                    class="text-brand-600">SaaS</span></a>
            <div class="hidden md:flex items-center gap-8">
                <a href="#fonctionnalites"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Fonctionnalités</a>
                <a href="#tarifs"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Tarifs</a>
                <a href="#temoignages"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Témoignages</a>
                <a href="#faq"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">FAQ</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="/login"
                    class="hidden sm:inline-flex text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors px-4 py-2">Connexion</a>
                <button onclick="openRegisterModal()"
                    class="btn-primary btn-shine bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-btn cursor-pointer">Essai
                    gratuit</button>
            </div>
        </div>
    </nav>

    
    <section class="relative min-h-[100dvh] flex items-center pt-20 pb-16 md:pt-24 md:pb-20 overflow-hidden">
        <div class="absolute inset-0 hero-gradient"></div>
        <div class="absolute inset-0 hero-dots opacity-40"></div>
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>

        <div class="relative max-w-6xl mx-auto px-5 sm:px-6 w-full">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                
                <div class="max-w-xl">
                    
                    <div
                        class="reveal inline-flex items-center gap-2 hero-badge text-xs font-semibold text-brand-700 px-4 py-2 rounded-full mb-6">
                        <span class="w-2 h-2 bg-accent-500 rounded-full animate-pulse"></span>
                        <span>Utilisé par 500+ entreprises africaines</span>
                    </div>

                    <h1 class="reveal text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold text-deep-900 tracking-tight leading-[1.1]"
                        style="transition-delay:80ms">
                        Facturez en <span class="text-brand-600">30 secondes</span>.<br>
                        Soyez payé <span class="relative inline-block"><span
                                class="relative z-10">instantanément</span><span
                                class="absolute bottom-1 left-0 w-full h-3 bg-accent-400/20 rounded-full -z-0"></span></span>.
                    </h1>

                    <p class="reveal mt-6 text-lg sm:text-xl text-slate-500 leading-relaxed max-w-lg"
                        style="transition-delay:160ms">
                        Créez des factures professionnelles, acceptez les paiements Mobile Money et carte, et suivez vos
                        revenus en temps réel.
                    </p>

                    
                    <div class="reveal flex flex-col sm:flex-row items-start sm:items-center gap-4 mt-8"
                        style="transition-delay:240ms">
                        <button onclick="openRegisterModal()"
                            class="btn-primary btn-shine group inline-flex items-center gap-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-7 py-4 rounded-2xl shadow-btn text-base cursor-pointer">
                            <span>Commencer gratuitement</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                        <div class="flex items-center gap-2 text-sm text-slate-400">
                            <svg class="w-4 h-4 text-accent-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Gratuit · Sans carte bancaire</span>
                        </div>
                    </div>

                    
                    <div class="reveal mt-10 flex items-center gap-6" style="transition-delay:320ms">
                        <div class="flex -space-x-2">
                            <div
                                class="w-8 h-8 rounded-full bg-brand-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-brand-700">
                                AK</div>
                            <div
                                class="w-8 h-8 rounded-full bg-accent-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-accent-600">
                                OD</div>
                            <div
                                class="w-8 h-8 rounded-full bg-amber-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-amber-700">
                                FT</div>
                            <div
                                class="w-8 h-8 rounded-full bg-rose-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-rose-700">
                                MK</div>
                        </div>
                        <div class="text-xs text-slate-400 leading-tight">
                            <div class="flex items-center gap-1 text-amber-400 mb-0.5">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <span>Noté 4.9/5 · 500+ avis</span>
                        </div>
                    </div>
                </div>

                
                <div class="reveal-scale mockup-3d hidden lg:block" style="transition-delay:200ms">
                    <div class="mockup-float mockup-shine">
                        <div class="bg-white rounded-3xl shadow-elevated border border-slate-200/60 overflow-hidden">
                            
                            <div class="flex items-center gap-2 px-4 py-3 bg-slate-50 border-b border-slate-100">
                                <div class="flex gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400"></span><span
                                        class="w-3 h-3 rounded-full bg-amber-400"></span><span
                                        class="w-3 h-3 rounded-full bg-green-400"></span></div>
                                <div class="flex-1 mx-8">
                                    <div
                                        class="bg-white rounded-lg px-3 py-1 text-[11px] text-slate-400 border border-slate-100 text-center">
                                        app.invoicesaas.com/dashboard</div>
                                </div>
                            </div>
                            
                            <div class="p-5 bg-surface-2">
                                
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    <div class="bg-white rounded-xl p-3 border border-slate-100">
                                        <div class="text-[10px] text-slate-400 font-medium">Revenus ce mois</div>
                                        <div class="text-lg font-extrabold text-deep-800 mt-1">2,4M <span
                                                class="text-[10px] text-slate-400 font-normal">FCFA</span></div>
                                        <div class="flex items-center gap-1 mt-1"><span
                                                class="text-[10px] text-accent-500 font-semibold">+18%</span><svg
                                                class="w-3 h-3 text-accent-500" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                            </svg></div>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-slate-100">
                                        <div class="text-[10px] text-slate-400 font-medium">Factures envoyées</div>
                                        <div class="text-lg font-extrabold text-deep-800 mt-1">147</div>
                                        <div class="flex items-center gap-1 mt-1"><span
                                                class="text-[10px] text-accent-500 font-semibold">+23%</span><svg
                                                class="w-3 h-3 text-accent-500" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                            </svg></div>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-slate-100">
                                        <div class="text-[10px] text-slate-400 font-medium">Taux de paiement</div>
                                        <div class="text-lg font-extrabold text-accent-600 mt-1">94%</div>
                                        <div class="w-full h-1.5 bg-accent-100 rounded-full mt-2">
                                            <div class="h-full w-[94%] bg-accent-500 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-xl p-4 border border-slate-100 mb-3">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-[11px] font-semibold text-slate-700">Revenus mensuels</span>
                                        <span class="text-[10px] text-slate-400">6 derniers mois</span>
                                    </div>
                                    <div class="flex items-end gap-2 h-20">
                                        <div class="flex-1 bg-brand-100 rounded-t-md" style="height:40%"></div>
                                        <div class="flex-1 bg-brand-200 rounded-t-md" style="height:55%"></div>
                                        <div class="flex-1 bg-brand-300 rounded-t-md" style="height:45%"></div>
                                        <div class="flex-1 bg-brand-400 rounded-t-md" style="height:70%"></div>
                                        <div class="flex-1 bg-brand-500 rounded-t-md" style="height:85%"></div>
                                        <div class="flex-1 bg-brand-600 rounded-t-md" style="height:100%"></div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
                                    <div class="px-3 py-2 border-b border-slate-50"><span
                                            class="text-[11px] font-semibold text-slate-700">Factures récentes</span>
                                    </div>
                                    <div class="divide-y divide-slate-50">
                                        <div class="flex items-center justify-between px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 rounded-md bg-accent-50 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-accent-600" fill="none"
                                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-medium text-slate-700">INV-0147 ·
                                                        Amadou S.</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-[11px] font-bold text-slate-800">350 000 FCFA</div>
                                                <div class="text-[9px] text-accent-500 font-medium">Payée · Wave</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 rounded-md bg-amber-50 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-amber-500" fill="none"
                                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-medium text-slate-700">INV-0146 ·
                                                        Fatou T.</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-[11px] font-bold text-slate-800">180 000 FCFA</div>
                                                <div class="text-[9px] text-amber-500 font-medium">En attente</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="hidden lg:flex justify-center mt-12">
                <div class="scroll-indicator text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                    </svg>
                </div>
            </div>
        </div>
    </section>

    
    <section class="relative py-16 bg-white border-y border-slate-100">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="reveal text-center" style="transition-delay:0ms">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-brand-50 mb-3">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold text-deep-900 counter"
                        data-target="<?php echo e($stats['users'] ?? 500); ?>">0</div>
                    <div class="text-sm text-slate-400 mt-1 font-medium">Utilisateurs actifs</div>
                </div>
                <div class="reveal text-center" style="transition-delay:80ms">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-accent-50 mb-3">
                        <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 21h19.5M3.75 3v18h16.5V3H3.75zm3 3.75h3v3h-3v-3zm6.75 0h3v3h-3v-3zm-6.75 6h3v3h-3v-3zm6.75 0h3v3h-3v-3z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold text-deep-900 counter"
                        data-target="<?php echo e($stats['tenants'] ?? 120); ?>">0</div>
                    <div class="text-sm text-slate-400 mt-1 font-medium">Entreprises</div>
                </div>
                <div class="reveal text-center" style="transition-delay:160ms">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-violet-50 mb-3">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold text-deep-900 counter"
                        data-target="<?php echo e($stats['invoices'] ?? 12500); ?>">0</div>
                    <div class="text-sm text-slate-400 mt-1 font-medium">Factures créées</div>
                </div>
                <div class="reveal text-center" style="transition-delay:240ms">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-50 mb-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold text-deep-900"><span class="counter"
                            data-target="<?php echo e(intval(($stats['total_invoiced'] ?? 850000000) / 1000000)); ?>">0</span>M
                    </div>
                    <div class="text-sm text-slate-400 mt-1 font-medium">FCFA facturés</div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-12 bg-surface">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <p class="reveal text-center text-xs font-semibold text-slate-400 uppercase tracking-wider mb-8">Moyens de
                paiement intégrés</p>
            <div class="reveal flex flex-wrap justify-center gap-3" style="transition-delay:80ms">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Orange Money', 'Wave', 'MTN MoMo', 'Moov Money', 'Free Money', 'M-Pesa', 'Visa', 'Mastercard', 'PayPal', 'Stripe', 'CinetPay', 'PayDunya']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span
                        class="payment-pill inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white border border-slate-100 text-xs font-medium text-slate-500 shadow-sm hover:border-brand-200 hover:text-brand-600 transition-all"><?php echo e($provider); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="py-20 md:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div
                    class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50 text-brand-600 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                    </svg>
                    Pourquoi nous choisir
                </div>
                <h2 class="reveal text-3xl sm:text-4xl font-extrabold text-deep-900 tracking-tight"
                    style="transition-delay:80ms">Finissez-en avec le chaos administratif</h2>
                <p class="reveal mt-4 text-lg text-slate-400 leading-relaxed" style="transition-delay:160ms">Passez
                    d'Excel et WhatsApp à un outil professionnel conçu pour l'Afrique.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 md:gap-8">
                
                <div class="ps-card reveal" style="transition-delay:0ms">
                    <div
                        class="bg-gradient-to-br from-slate-50 to-red-50/30 rounded-2xl p-8 border border-slate-200/60 h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Sans InvoiceSaaS</h3>
                        </div>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 w-5 h-5 rounded-full bg-red-100 flex-shrink-0 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-red-400" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg></span>
                                <span class="text-sm text-slate-500">Factures manuelles sur Word ou Excel, pleines
                                    d'erreurs</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 w-5 h-5 rounded-full bg-red-100 flex-shrink-0 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-red-400" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg></span>
                                <span class="text-sm text-slate-500">Relances de paiement par WhatsApp, aucun
                                    suivi</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 w-5 h-5 rounded-full bg-red-100 flex-shrink-0 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-red-400" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg></span>
                                <span class="text-sm text-slate-500">Aucune visibilité sur le chiffre d'affaires
                                    réel</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 w-5 h-5 rounded-full bg-red-100 flex-shrink-0 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-red-400" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg></span>
                                <span class="text-sm text-slate-500">Des heures perdues chaque semaine en tâches
                                    administratives</span>
                            </li>
                        </ul>
                    </div>
                </div>

                
                <div class="ps-card reveal" style="transition-delay:120ms">
                    <div
                        class="bg-gradient-to-br from-brand-50/50 to-accent-50/30 rounded-2xl p-8 border border-brand-100/60 h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-accent-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Avec InvoiceSaaS</h3>
                        </div>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 w-5 h-5 rounded-full bg-accent-100 flex-shrink-0 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-accent-600" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg></span>
                                <span class="text-sm text-slate-600 font-medium">Factures professionnelles créées en 30
                                    secondes</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 w-5 h-5 rounded-full bg-accent-100 flex-shrink-0 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-accent-600" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg></span>
                                <span class="text-sm text-slate-600 font-medium">Paiements Mobile Money & Carte en un
                                    clic</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 w-5 h-5 rounded-full bg-accent-100 flex-shrink-0 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-accent-600" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg></span>
                                <span class="text-sm text-slate-600 font-medium">Tableau de bord temps réel avec
                                    analytics</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-0.5 w-5 h-5 rounded-full bg-accent-100 flex-shrink-0 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-accent-600" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg></span>
                                <span class="text-sm text-slate-600 font-medium">Relances automatiques, export CSV,
                                    multi-devises</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section id="fonctionnalites" class="py-20 md:py-28 bg-surface">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div
                    class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50 text-brand-600 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                    Fonctionnalités
                </div>
                <h2 class="reveal text-3xl sm:text-4xl font-extrabold text-deep-900 tracking-tight"
                    style="transition-delay:80ms">Tout ce qu'il faut pour facturer comme un pro</h2>
                <p class="reveal mt-4 text-lg text-slate-400 leading-relaxed" style="transition-delay:160ms">Des
                    outils puissants, pensés pour les entrepreneurs africains.</p>
            </div>

            <div class="stagger-grid grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                
                <div
                    class="feature-card group bg-white rounded-2xl p-7 border border-slate-100 hover:border-brand-200/60 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div
                        class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center mb-5 group-hover:bg-brand-100 transition-colors">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <span
                        class="inline-block text-[10px] font-bold uppercase tracking-wider text-brand-600 bg-brand-50 px-2 py-0.5 rounded mb-3">Essentiel</span>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Facturation automatisée</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Créez et envoyez des factures conformes en
                        quelques clics. Templates personnalisables avec votre logo.</p>
                </div>
                
                <div
                    class="feature-card group bg-white rounded-2xl p-7 border border-slate-100 hover:border-accent-200/60 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div
                        class="w-12 h-12 rounded-2xl bg-accent-50 flex items-center justify-center mb-5 group-hover:bg-accent-100 transition-colors">
                        <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                    </div>
                    <span
                        class="inline-block text-[10px] font-bold uppercase tracking-wider text-accent-600 bg-accent-50 px-2 py-0.5 rounded mb-3">Paiements</span>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Mobile Money & Carte</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Orange Money, Wave, MTN MoMo, Visa, Mastercard.
                        Vos clients paient en un clic, vous êtes crédité instantanément.</p>
                </div>
                
                <div
                    class="feature-card group bg-white rounded-2xl p-7 border border-slate-100 hover:border-violet-200/60 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div
                        class="w-12 h-12 rounded-2xl bg-violet-50 flex items-center justify-center mb-5 group-hover:bg-violet-100 transition-colors">
                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                        </svg>
                    </div>
                    <span
                        class="inline-block text-[10px] font-bold uppercase tracking-wider text-violet-600 bg-violet-50 px-2 py-0.5 rounded mb-3">Récurrent</span>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Facturation récurrente</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Automatisez vos factures mensuelles. Définissez
                        la fréquence, le système fait le reste.</p>
                </div>
                
                <div
                    class="feature-card group bg-white rounded-2xl p-7 border border-slate-100 hover:border-amber-200/60 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center mb-5 group-hover:bg-amber-100 transition-colors">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <span
                        class="inline-block text-[10px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-0.5 rounded mb-3">Analytics</span>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Suivi des paiements</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Tableau de bord en temps réel. Visualisez vos
                        revenus, factures impayées et tendances de trésorerie.</p>
                </div>
                
                <div
                    class="feature-card group bg-white rounded-2xl p-7 border border-slate-100 hover:border-rose-200/60 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div
                        class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center mb-5 group-hover:bg-rose-100 transition-colors">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </div>
                    <span
                        class="inline-block text-[10px] font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded mb-3">Export</span>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Export PDF & CSV</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Exportez vos factures en PDF professionnel ou vos
                        données en CSV pour votre comptable.</p>
                </div>
                
                <div
                    class="feature-card group bg-white rounded-2xl p-7 border border-slate-100 hover:border-cyan-200/60 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div
                        class="w-12 h-12 rounded-2xl bg-cyan-50 flex items-center justify-center mb-5 group-hover:bg-cyan-100 transition-colors">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <span
                        class="inline-block text-[10px] font-bold uppercase tracking-wider text-cyan-600 bg-cyan-50 px-2 py-0.5 rounded mb-3">Sécurité</span>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Multi-tenant & 2FA</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Chaque entreprise est isolée. Authentification à
                        deux facteurs et chiffrement des données inclus.</p>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-20 md:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div
                    class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50 text-brand-600 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    3 étapes simples
                </div>
                <h2 class="reveal text-3xl sm:text-4xl font-extrabold text-deep-900 tracking-tight"
                    style="transition-delay:80ms">Opérationnel en 2 minutes</h2>
                <p class="reveal mt-4 text-lg text-slate-400 leading-relaxed" style="transition-delay:160ms">Pas de
                    configuration complexe. Inscrivez-vous et commencez à facturer.</p>
            </div>

            <div class="steps-container grid md:grid-cols-3 gap-8 relative">
                
                <div
                    class="hidden md:block absolute top-16 left-[16.666%] right-[16.666%] h-0.5 bg-gradient-to-r from-brand-200 via-accent-200 to-violet-200 z-0">
                </div>

                
                <div class="reveal relative z-10 text-center" style="transition-delay:0ms">
                    <div
                        class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-600 text-white text-xl font-extrabold mb-6 shadow-lg shadow-brand-200/40 ring-4 ring-white">
                        1</div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Créez votre compte</h3>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-xs mx-auto">Inscription gratuite en 30
                        secondes. Aucune carte bancaire requise.</p>
                </div>
                
                <div class="reveal relative z-10 text-center" style="transition-delay:120ms">
                    <div
                        class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-accent-500 text-white text-xl font-extrabold mb-6 shadow-lg shadow-accent-200/40 ring-4 ring-white">
                        2</div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Personnalisez & Facturez</h3>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-xs mx-auto">Ajoutez votre logo, configurez
                        vos services et envoyez votre première facture.</p>
                </div>
                
                <div class="reveal relative z-10 text-center" style="transition-delay:240ms">
                    <div
                        class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-violet-600 text-white text-xl font-extrabold mb-6 shadow-lg shadow-violet-200/40 ring-4 ring-white">
                        3</div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Encaissez & Suivez</h3>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-xs mx-auto">Vos clients paient en ligne.
                        Suivez tout depuis votre tableau de bord.</p>
                </div>
            </div>
        </div>
    </section>

    
    <section id="tarifs" class="py-20 md:py-28 bg-surface">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <div
                    class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50 text-brand-600 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                    Tarifs transparents
                </div>
                <h2 class="reveal text-3xl sm:text-4xl font-extrabold text-deep-900 tracking-tight"
                    style="transition-delay:80ms">Un plan pour chaque ambition</h2>
                <p class="reveal mt-4 text-lg text-slate-400 leading-relaxed" style="transition-delay:160ms">Commencez
                    gratuitement, évoluez quand vous êtes prêt.</p>
            </div>

            
            <div class="reveal flex items-center justify-center gap-3 mb-12" style="transition-delay:200ms">
                <span class="text-sm font-semibold text-slate-700" id="label-monthly">Mensuel</span>
                <button onclick="toggleBilling()" id="billing-toggle"
                    class="relative w-12 h-6 bg-slate-200 rounded-full transition-colors cursor-pointer"
                    role="switch" aria-checked="false">
                    <span id="billing-dot"
                        class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"></span>
                </button>
                <span class="text-sm font-medium text-slate-400" id="label-annual">Annuel <span
                        class="text-accent-600 font-semibold">-20%</span></span>
            </div>

            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                
                <div class="reveal bg-white rounded-2xl border border-slate-200/60 p-8 flex flex-col"
                    style="transition-delay:0ms">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Starter</h3>
                        <p class="text-sm text-slate-400">Pour démarrer votre activité</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-extrabold text-deep-900">Gratuit</span>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>5 factures / mois</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>1 utilisateur</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Export PDF</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Support email</li>
                    </ul>
                    <button onclick="openRegisterModal('starter')"
                        class="w-full py-3 rounded-xl border-2 border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">Commencer
                        gratuitement</button>
                </div>

                
                <div class="reveal bg-white rounded-2xl border-2 border-brand-500 p-8 flex flex-col relative shadow-xl shadow-brand-100/30"
                    style="transition-delay:120ms">
                    <div
                        class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-brand-600 text-white text-xs font-bold rounded-full">
                        Populaire</div>
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Pro</h3>
                        <p class="text-sm text-slate-400">Pour les indépendants & PME</p>
                    </div>
                    <div class="mb-6">
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-extrabold text-deep-900 price-amount" data-monthly="15200"
                                data-annual="12160">15 200</span>
                            <span class="text-sm text-slate-400 font-medium">FCFA/<span
                                    class="price-period">mois</span></span>
                        </div>
                        <div class="text-xs text-slate-300 line-through mt-1 price-original" data-monthly="19000"
                            data-annual="15200">19 000 FCFA</div>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-center gap-2.5 text-sm text-slate-600 font-medium"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Factures illimitées</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-600 font-medium"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>3 utilisateurs</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-600 font-medium"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Mobile Money & Carte</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-600 font-medium"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Facturation récurrente</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-600 font-medium"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Export PDF & CSV</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-600 font-medium"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Support prioritaire</li>
                    </ul>
                    <button onclick="openRegisterModal('pro')"
                        class="btn-primary btn-shine w-full py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-btn cursor-pointer">Choisir
                        Pro</button>
                </div>

                
                <div class="reveal bg-white rounded-2xl border border-slate-200/60 p-8 flex flex-col"
                    style="transition-delay:240ms">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Enterprise</h3>
                        <p class="text-sm text-slate-400">Pour les grandes équipes</p>
                    </div>
                    <div class="mb-6">
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-extrabold text-deep-900 price-amount" data-monthly="52000"
                                data-annual="41600">52 000</span>
                            <span class="text-sm text-slate-400 font-medium">FCFA/<span
                                    class="price-period">mois</span></span>
                        </div>
                        <div class="text-xs text-slate-300 line-through mt-1 price-original" data-monthly="65000"
                            data-annual="52000">65 000 FCFA</div>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Tout de Pro, plus :</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Utilisateurs illimités</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Multi-devises (FCFA, EUR, USD)</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>API & Webhooks</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>2FA & Audit trail</li>
                        <li class="flex items-center gap-2.5 text-sm text-slate-500"><svg
                                class="w-4 h-4 text-accent-500 flex-shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>Account manager dédié</li>
                    </ul>
                    <button onclick="openRegisterModal('enterprise')"
                        class="w-full py-3 rounded-xl border-2 border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">Choisir
                        Enterprise</button>
                </div>
            </div>
        </div>
    </section>

    
    <section id="temoignages" class="py-20 md:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div
                    class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50 text-brand-600 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                    </svg>
                    Témoignages
                </div>
                <h2 class="reveal text-3xl sm:text-4xl font-extrabold text-deep-900 tracking-tight"
                    style="transition-delay:80ms">Ils nous font confiance</h2>
                <p class="reveal mt-4 text-lg text-slate-400 leading-relaxed" style="transition-delay:160ms">Des
                    entrepreneurs africains qui ont transformé leur facturation.</p>
            </div>

            <div class="stagger-grid grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                
                <div class="testimonial-card bg-surface rounded-2xl p-7 border border-slate-100">
                    <div class="flex items-center gap-1 mb-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <blockquote class="text-sm text-slate-500 leading-relaxed mb-6">"InvoiceSaaS a révolutionné ma
                        facturation. Mes clients paient via Wave ou Orange Money en 2 clics. J'ai réduit mes impayés de
                        60%."</blockquote>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-sm font-bold text-brand-700">
                            AK</div>
                        <div>
                            <div class="text-sm font-semibold text-slate-700">Aminata K.</div>
                            <div class="text-xs text-slate-400">Designer freelance · Dakar</div>
                        </div>
                    </div>
                </div>

                
                <div class="testimonial-card bg-surface rounded-2xl p-7 border border-slate-100">
                    <div class="flex items-center gap-1 mb-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <blockquote class="text-sm text-slate-500 leading-relaxed mb-6">"L'intégration Mobile Money est
                        parfaite. On facture nos clients au Sénégal, en Côte d'Ivoire et au Mali depuis un seul
                        dashboard."</blockquote>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-accent-100 flex items-center justify-center text-sm font-bold text-accent-600">
                            OD</div>
                        <div>
                            <div class="text-sm font-semibold text-slate-700">Oumar D.</div>
                            <div class="text-xs text-slate-400">CEO, AgenceDigi · Abidjan</div>
                        </div>
                    </div>
                </div>

                
                <div class="testimonial-card bg-surface rounded-2xl p-7 border border-slate-100">
                    <div class="flex items-center gap-1 mb-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <blockquote class="text-sm text-slate-500 leading-relaxed mb-6">"Avant, je passais 2h par jour sur
                        la facturation. Maintenant c'est 5 minutes. La facturation récurrente est un game-changer."
                    </blockquote>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-sm font-bold text-amber-700">
                            FT</div>
                        <div>
                            <div class="text-sm font-semibold text-slate-700">Fatou T.</div>
                            <div class="text-xs text-slate-400">Consultante IT · Bamako</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section id="faq" class="py-20 md:py-28 bg-surface">
        <div class="max-w-3xl mx-auto px-5 sm:px-6">
            <div class="text-center mb-14">
                <div
                    class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50 text-brand-600 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                    FAQ
                </div>
                <h2 class="reveal text-3xl sm:text-4xl font-extrabold text-deep-900 tracking-tight"
                    style="transition-delay:80ms">Questions fréquentes</h2>
            </div>

            <div class="faq-list space-y-3">
                <?php
                    $faqs = [
                        [
                            'q' => 'Est-ce vraiment gratuit pour commencer ?',
                            'a' =>
                                'Oui ! Le plan Starter est 100% gratuit avec 5 factures par mois. Aucune carte bancaire requise. Vous pouvez upgrader à tout moment.',
                        ],
                        [
                            'q' => 'Quels moyens de paiement sont acceptés ?',
                            'a' =>
                                'Orange Money, Wave, MTN MoMo, Moov Money, Free Money, M-Pesa, Visa, Mastercard, PayPal et Stripe. Nous ajoutons régulièrement de nouveaux fournisseurs.',
                        ],
                        [
                            'q' => 'Mes données sont-elles sécurisées ?',
                            'a' =>
                                "Absolument. Nous utilisons le chiffrement AES-256, l'authentification 2FA, et chaque entreprise est isolée dans un environnement multi-tenant sécurisé.",
                        ],
                        [
                            'q' => 'Puis-je facturer en plusieurs devises ?',
                            'a' =>
                                "Oui, le plan Enterprise supporte FCFA, EUR, USD et d'autres devises. Les taux de conversion sont mis à jour automatiquement.",
                        ],
                        [
                            'q' => 'Comment fonctionne le support ?',
                            'a' =>
                                'Plan Starter : email sous 48h. Plan Pro : support prioritaire sous 4h. Plan Enterprise : account manager dédié + chat en direct.',
                        ],
                    ];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="reveal faq-item bg-white rounded-xl border border-slate-100 overflow-hidden transition-all"
                        style="transition-delay:<?php echo e($i * 60); ?>ms">
                        <button onclick="toggleFaq(this)"
                            class="w-full flex items-center gap-4 px-6 py-4 text-left cursor-pointer group">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-sm font-bold text-brand-600 group-hover:bg-brand-100 transition-colors"><?php echo e($i + 1); ?></span>
                            <span class="flex-1 text-sm font-semibold text-slate-700"><?php echo e($faq['q']); ?></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform faq-chevron flex-shrink-0"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <div class="faq-answer px-6 pb-0 max-h-0 overflow-hidden transition-all duration-300">
                            <p class="text-sm text-slate-400 leading-relaxed pb-4 pl-12"><?php echo e($faq['a']); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="reveal text-center mt-10" style="transition-delay:400ms">
                <p class="text-sm text-slate-400">Vous avez une autre question ? <a
                        href="mailto:contact@invoicesaas.com"
                        class="text-brand-600 font-semibold hover:text-brand-700 transition-colors">Contactez-nous</a>
                </p>
            </div>
        </div>
    </section>

    
    <section class="py-20 md:py-28 bg-gradient-to-b from-deep-900 to-deep-800 relative overflow-hidden">
        <div class="absolute inset-0 hero-dots opacity-10"></div>
        <div class="relative max-w-3xl mx-auto px-5 sm:px-6 text-center">
            <h2 class="reveal text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">Prêt à
                transformer<br>votre facturation ?</h2>
            <p class="reveal mt-5 text-lg text-slate-300 leading-relaxed max-w-lg mx-auto"
                style="transition-delay:80ms">Rejoignez des centaines d'entrepreneurs africains qui facturent plus vite
                et sont payés plus rapidement.</p>
            <div class="reveal mt-8 flex flex-col sm:flex-row items-center justify-center gap-4"
                style="transition-delay:160ms">
                <button onclick="openRegisterModal()"
                    class="btn-primary btn-shine group inline-flex items-center gap-2.5 bg-white text-deep-900 font-semibold px-7 py-4 rounded-2xl text-base cursor-pointer hover:bg-slate-50 transition-colors shadow-lg">
                    <span>Commencer gratuitement</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                        stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </div>
            <div class="reveal flex items-center justify-center gap-6 mt-8 text-sm text-slate-400"
                style="transition-delay:240ms">
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-accent-400" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>Gratuit</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-accent-400" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>Sans engagement</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-accent-400" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>Setup 2 min</span>
            </div>
        </div>
    </section>

    
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
                            class="w-8 h-8 rounded-lg bg-deep-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-all"><svg
                                class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg></a>
                        <a href="#"
                            class="w-8 h-8 rounded-lg bg-deep-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-all"><svg
                                class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4">Produit</h4>
                    <ul class="space-y-2.5">
                        <li><a href="#fonctionnalites"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Fonctionnalités</a>
                        </li>
                        <li><a href="#tarifs"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Tarifs</a></li>
                        <li><a href="#temoignages"
                                class="text-sm text-slate-400 hover:text-white transition-colors">Témoignages</a></li>
                        <li><a href="#faq"
                                class="text-sm text-slate-400 hover:text-white transition-colors">FAQ</a></li>
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

    
    <div id="stickyCta" class="sticky-cta fixed bottom-0 left-0 right-0 z-40 md:hidden translate-y-full">
        <div
            class="bg-white/95 backdrop-blur-lg border-t border-slate-200 px-5 py-3 flex items-center justify-between">
            <div>
                <div class="text-sm font-bold text-slate-800">Essai gratuit</div>
                <div class="text-[11px] text-slate-400">Sans carte bancaire</div>
            </div>
            <button onclick="openRegisterModal()"
                class="btn-primary bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-btn cursor-pointer">Commencer</button>
        </div>
    </div>

    
    <div id="registerModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        
        <div class="modal-overlay absolute inset-0 bg-deep-900/60 backdrop-blur-md" onclick="closeRegisterModal()">
        </div>

        
        <div class="modal-content relative w-full max-w-lg mx-4 max-h-[90vh]">
            <div class="bg-white rounded-3xl shadow-elevated overflow-hidden overflow-y-auto max-h-[90vh]">

                
                <div class="modal-header-gradient px-8 pt-7 pb-6 relative">
                    
                    <span class="modal-particle"></span>
                    <span class="modal-particle"></span>
                    <span class="modal-particle"></span>
                    <span class="modal-particle"></span>
                    <span class="modal-particle"></span>

                    
                    <button onclick="closeRegisterModal()"
                        class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all cursor-pointer backdrop-blur-sm group">
                        <svg class="w-4 h-4 text-white/70 group-hover:text-white group-hover:rotate-90 transition-all duration-300"
                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-11 h-11 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-extrabold text-white">Créer votre compte</h3>
                                <p class="text-sm text-white/60">Commencez à facturer en 2 minutes</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 mt-1">
                            <div class="flex items-center gap-1.5 text-xs text-white/50">
                                <span
                                    class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px] font-bold text-white">✓</span>
                                <span class="text-white/70 font-medium">Choisir</span>
                            </div>
                            <div class="w-6 h-px bg-white/20"></div>
                            <div class="flex items-center gap-1.5 text-xs text-white/50">
                                <span
                                    class="w-5 h-5 rounded-full bg-white flex items-center justify-center text-[10px] font-bold text-brand-600">2</span>
                                <span class="text-white font-medium">S'inscrire</span>
                            </div>
                            <div class="w-6 h-px bg-white/20"></div>
                            <div class="flex items-center gap-1.5 text-xs text-white/30">
                                <span
                                    class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-bold">3</span>
                                <span>Facturer</span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="px-8 py-6">

                    
                    <div class="grid grid-cols-3 gap-3 mb-6" id="planSelector">
                        <button onclick="selectPlan('starter')" data-plan="starter"
                            class="modal-plan-btn plan-btn relative">
                            <div
                                class="plan-check absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-brand-600 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="relative z-10">
                                <div
                                    class="plan-icon-wrap w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                </div>
                                <div class="text-xs font-bold text-slate-700 mb-0.5">Starter</div>
                                <div class="text-[10px] text-slate-400 font-medium">Gratuit</div>
                            </div>
                        </button>

                        <button onclick="selectPlan('pro')" data-plan="pro"
                            class="modal-plan-btn plan-btn selected relative">
                            <div
                                class="plan-check absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-brand-600 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            
                            <div
                                class="absolute -top-2.5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full bg-brand-600 text-[9px] font-bold text-white uppercase tracking-wider whitespace-nowrap">
                                Populaire</div>
                            <div class="relative z-10 mt-1">
                                <div
                                    class="plan-icon-wrap w-8 h-8 rounded-xl bg-brand-100 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                                    </svg>
                                </div>
                                <div class="text-xs font-bold text-brand-700 mb-0.5">Pro</div>
                                <div class="text-[10px] text-brand-500 font-medium">19 000 XOF</div>
                            </div>
                        </button>

                        <button onclick="selectPlan('enterprise')" data-plan="enterprise"
                            class="modal-plan-btn plan-btn relative">
                            <div
                                class="plan-check absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-brand-600 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="relative z-10">
                                <div
                                    class="plan-icon-wrap w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" />
                                    </svg>
                                </div>
                                <div class="text-xs font-bold text-slate-700 mb-0.5">Enterprise</div>
                                <div class="text-[10px] text-slate-400 font-medium">65 000 XOF</div>
                            </div>
                        </button>
                    </div>

                    
                    <form id="registerForm" action="<?php echo e(route('register.with-plan')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="plan" id="selectedPlan" value="pro">

                        <div class="space-y-3.5">
                            
                            <div class="modal-field relative">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nom de l'entreprise
                                    *</label>
                                <div class="relative">
                                    <span class="modal-input-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" />
                                        </svg>
                                    </span>
                                    <input type="text" name="company_name" required
                                        placeholder="Ex: Ma Société SARL" class="modal-input">
                                </div>
                            </div>

                            
                            <div class="modal-field relative">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nom complet</label>
                                <div class="relative">
                                    <span class="modal-input-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" />
                                        </svg>
                                    </span>
                                    <input type="text" name="name" required placeholder="Aminata Koné"
                                        class="modal-input">
                                </div>
                            </div>

                            
                            <div class="modal-field relative">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email
                                    professionnel</label>
                                <div class="relative">
                                    <span class="modal-input-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </span>
                                    <input type="email" name="email" required
                                        placeholder="aminata@exemple.com" class="modal-input">
                                </div>
                            </div>

                            
                            <div class="modal-field relative">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mot de passe</label>
                                <div class="relative">
                                    <span class="modal-input-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </span>
                                    <input type="password" name="password" id="registerPassword" required
                                        minlength="8" placeholder="Min. 8 caractères"
                                        oninput="updatePasswordStrength(this.value)" class="modal-input">
                                    
                                    <button type="button" onclick="togglePasswordVisibility()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                                        <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg id="eyeOffIcon" class="w-4 h-4 hidden" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="mt-2 flex gap-1" id="strengthBars">
                                    <div class="strength-bar flex-1 bg-slate-100 rounded-full"></div>
                                    <div class="strength-bar flex-1 bg-slate-100 rounded-full"></div>
                                    <div class="strength-bar flex-1 bg-slate-100 rounded-full"></div>
                                    <div class="strength-bar flex-1 bg-slate-100 rounded-full"></div>
                                </div>
                                <p id="strengthText"
                                    class="text-[10px] mt-1 font-medium text-slate-400 transition-colors"></p>
                            </div>

                            
                            <div class="modal-field relative">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Confirmer le mot de
                                    passe *</label>
                                <div class="relative">
                                    <span class="modal-input-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </span>
                                    <input type="password" name="password_confirmation"
                                        id="registerPasswordConfirm" required minlength="8"
                                        placeholder="Répétez le mot de passe" class="modal-input">
                                    
                                    <button type="button" onclick="togglePasswordConfirmVisibility()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                                        <svg id="eyeIconConfirm" class="w-4 h-4" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg id="eyeOffIconConfirm" class="w-4 h-4 hidden" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            
                            <div class="modal-field flex items-start gap-3">
                                <input type="checkbox" name="terms" required
                                    class="mt-0.5 h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded cursor-pointer">
                                <label class="text-xs text-slate-600 leading-snug">
                                    J'accepte les <a href="/conditions-generales"
                                        class="font-semibold text-brand-600 hover:text-brand-700 transition-colors"
                                        target="_blank">conditions
                                        générales</a> et la <a href="/politique-confidentialite"
                                        class="font-semibold text-brand-600 hover:text-brand-700 transition-colors"
                                        target="_blank">politique
                                        de confidentialité</a>
                                </label>
                            </div>

                            
                            <div class="modal-field pt-1">
                                <button type="submit" id="registerSubmitBtn"
                                    class="modal-submit w-full py-3.5 rounded-2xl bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-white font-bold shadow-btn cursor-pointer text-sm flex items-center justify-center gap-2">
                                    <span id="registerBtnText" class="flex items-center gap-2">
                                        Créer mon compte
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </span>
                                    <span id="registerSpinner" class="hidden"><span class="spinner"></span></span>
                                </button>
                            </div>
                        </div>
                    </form>

                    
                    <div class="modal-trust mt-5 flex items-center justify-center gap-4 text-[10px] text-slate-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-accent-500" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            SSL 256-bit
                        </span>
                        <span class="w-px h-3 bg-slate-200"></span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-accent-500" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            Données protégées
                        </span>
                        <span class="w-px h-3 bg-slate-200"></span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-accent-500" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Essai gratuit
                        </span>
                    </div>

                    
                    <p class="text-xs text-slate-400 text-center mt-4">Déjà un compte ?
                        <a href="/login"
                            class="text-brand-600 font-semibold hover:text-brand-700 transition-colors">Connexion</a>
                    </p>
                </div>
            </div>
        </div>

        
        <div id="modalConfetti" class="absolute inset-0 pointer-events-none overflow-hidden z-[110]"></div>
    </div>

    
    <script>
        /* ─── Billing Toggle ─── */
        let isAnnual = false;

        function toggleBilling() {
            isAnnual = !isAnnual;
            const toggle = document.getElementById('billing-toggle');
            const dot = document.getElementById('billing-dot');
            toggle.setAttribute('aria-checked', isAnnual);
            if (isAnnual) {
                toggle.classList.add('bg-brand-600');
                toggle.classList.remove('bg-slate-200');
                dot.style.transform = 'translateX(24px)';
            } else {
                toggle.classList.remove('bg-brand-600');
                toggle.classList.add('bg-slate-200');
                dot.style.transform = 'translateX(0)';
            }
            document.getElementById('label-monthly').classList.toggle('text-slate-400', isAnnual);
            document.getElementById('label-monthly').classList.toggle('text-slate-700', !isAnnual);
            document.getElementById('label-monthly').classList.toggle('font-semibold', !isAnnual);
            document.getElementById('label-monthly').classList.toggle('font-medium', isAnnual);
            document.getElementById('label-annual').classList.toggle('text-slate-700', isAnnual);
            document.getElementById('label-annual').classList.toggle('font-semibold', isAnnual);
            document.getElementById('label-annual').classList.toggle('text-slate-400', !isAnnual);
            document.getElementById('label-annual').classList.toggle('font-medium', !isAnnual);
            document.querySelectorAll('.price-amount').forEach(el => {
                const val = isAnnual ? el.dataset.annual : el.dataset.monthly;
                el.textContent = Number(val).toLocaleString('fr-FR');
            });
            document.querySelectorAll('.price-original').forEach(el => {
                const val = isAnnual ? el.dataset.annual : el.dataset.monthly;
                el.textContent = Number(val).toLocaleString('fr-FR') + ' FCFA';
            });
            document.querySelectorAll('.price-period').forEach(el => {
                el.textContent = isAnnual ? 'mois' : 'mois';
            });
        }

        /* ─── FAQ Toggle ─── */
        function toggleFaq(btn) {
            const item = btn.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const chevron = item.querySelector('.faq-chevron');
            const isOpen = item.classList.contains('faq-active');
            document.querySelectorAll('.faq-item.faq-active').forEach(el => {
                el.classList.remove('faq-active');
                el.querySelector('.faq-answer').style.maxHeight = '0';
                el.querySelector('.faq-answer').style.paddingBottom = '0';
                el.querySelector('.faq-chevron').style.transform = 'rotate(0deg)';
            });
            if (!isOpen) {
                item.classList.add('faq-active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                answer.style.paddingBottom = '0';
                chevron.style.transform = 'rotate(180deg)';
            }
        }

        /* ─── Modal ─── */
        function openRegisterModal(plan) {
            const modal = document.getElementById('registerModal');
            const overlay = modal.querySelector('.modal-overlay');
            const content = modal.querySelector('.modal-content');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // Reset closing state
            content.classList.remove('closing');

            // Trigger animations with slight delay for CSS transition
            requestAnimationFrame(() => {
                overlay.classList.add('active');
                content.classList.add('active');
            });

            // Launch confetti
            launchConfetti();

            if (plan) selectPlan(plan);
        }

        function closeRegisterModal() {
            const modal = document.getElementById('registerModal');
            const overlay = modal.querySelector('.modal-overlay');
            const content = modal.querySelector('.modal-content');

            overlay.classList.remove('active');
            content.classList.remove('active');
            content.classList.add('closing');

            // Wait for animation to finish before hiding
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                content.classList.remove('closing');
                document.body.style.overflow = '';
            }, 350);
        }

        function selectPlan(plan) {
            document.getElementById('selectedPlan').value = plan;
            document.querySelectorAll('.plan-btn').forEach(btn => {
                if (btn.dataset.plan === plan) {
                    btn.classList.add('selected');
                } else {
                    btn.classList.remove('selected');
                }
            });

            // Update button text based on plan
            const btnText = document.getElementById('registerBtnText');
            if (plan === 'starter') {
                btnText.innerHTML =
                    'Commencer gratuitement <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>';
            } else if (plan === 'pro') {
                btnText.innerHTML =
                    'Créer mon compte <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>';
            } else {
                btnText.innerHTML =
                    'Créer mon compte <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>';
            }
        }

        /* ─── Password Strength ─── */
        function updatePasswordStrength(value) {
            const bars = document.querySelectorAll('#strengthBars .strength-bar');
            const text = document.getElementById('strengthText');
            let score = 0;

            if (value.length >= 8) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
            const labels = ['Faible', 'Moyen', 'Bon', 'Excellent'];
            const textColors = ['text-red-500', 'text-amber-500', 'text-blue-500', 'text-emerald-500'];

            bars.forEach((bar, i) => {
                if (i < score) {
                    bar.style.background = colors[score - 1];
                    bar.style.transform = 'scaleX(1)';
                } else {
                    bar.style.background = '#e2e8f0';
                    bar.style.transform = 'scaleX(1)';
                }
            });

            text.className = 'text-[10px] mt-1 font-medium transition-colors';
            if (value.length > 0 && score > 0) {
                text.textContent = labels[score - 1];
                text.classList.add(textColors[score - 1]);
            } else {
                text.textContent = '';
            }
        }

        /* ─── Toggle Password Visibility ─── */
        function togglePasswordVisibility() {
            const input = document.getElementById('registerPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        function togglePasswordConfirmVisibility() {
            const input = document.getElementById('registerPasswordConfirm');
            const eyeIcon = document.getElementById('eyeIconConfirm');
            const eyeOffIcon = document.getElementById('eyeOffIconConfirm');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        /* ─── Confetti Burst ─── */
        function launchConfetti() {
            const container = document.getElementById('modalConfetti');
            container.innerHTML = '';
            const colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#3b82f6'];
            for (let i = 0; i < 20; i++) {
                const piece = document.createElement('div');
                piece.className = 'confetti-piece';
                piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                piece.style.left = (40 + Math.random() * 20) + '%';
                piece.style.top = (30 + Math.random() * 20) + '%';
                piece.style.setProperty('--tx', (Math.random() * 300 - 150) + 'px');
                piece.style.setProperty('--ty', (Math.random() * 300 - 150) + 'px');
                piece.style.setProperty('--tr', (Math.random() * 720 - 360) + 'deg');
                piece.style.animationDelay = (Math.random() * .2) + 's';
                piece.style.borderRadius = Math.random() > .5 ? '50%' : '1px';
                piece.style.width = (4 + Math.random() * 4) + 'px';
                piece.style.height = (4 + Math.random() * 4) + 'px';
                container.appendChild(piece);
            }
            // Clean up after animation
            setTimeout(() => {
                container.innerHTML = '';
            }, 1200);
        }

        /* ─── Form Submit ─── */
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('registerSubmitBtn');
            document.getElementById('registerBtnText').classList.add('hidden');
            document.getElementById('registerSpinner').classList.remove('hidden');
            btn.disabled = true;
        });

        /* ─── Intersection Observer ─── */
        document.addEventListener('DOMContentLoaded', function() {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        if (entry.target.classList.contains('stagger-grid')) {
                            Array.from(entry.target.children).forEach((child, i) => {
                                child.style.transitionDelay = (i * 80) + 'ms';
                                child.classList.add('visible');
                            });
                        }
                        obs.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -40px 0px'
            });
            document.querySelectorAll(
                '.reveal, .reveal-left, .reveal-right, .reveal-scale, .ps-card, .stagger-grid, .steps-container, .faq-list'
            ).forEach(el => obs.observe(el));

            /* ─── Counters ─── */
            const counterObs = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const target = parseInt(el.dataset.target);
                        const dur = 2000;
                        const start = Date.now();
                        const tick = () => {
                            const elapsed = Date.now() - start;
                            const progress = Math.min(elapsed / dur, 1);
                            const eased = 1 - Math.pow(1 - progress, 3);
                            el.textContent = Math.floor(eased * target).toLocaleString('fr-FR');
                            if (progress < 1) requestAnimationFrame(tick);
                            else el.textContent = target.toLocaleString('fr-FR');
                        };
                        tick();
                        counterObs.unobserve(el);
                    }
                });
            }, {
                threshold: 0.5
            });
            document.querySelectorAll('.counter').forEach(el => counterObs.observe(el));

            /* ─── Smooth scroll ─── */
            document.querySelectorAll('a[href^="#"]').forEach(a => {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });

            /* ─── Nav scroll effect ─── */
            const nav = document.getElementById('mainNav');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    nav.classList.add('nav-scrolled');
                } else {
                    nav.classList.remove('nav-scrolled');
                }
            }, {
                passive: true
            });

            /* ─── Sticky CTA mobile ─── */
            const sticky = document.getElementById('stickyCta');
            if (sticky) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 600) {
                        sticky.classList.add('visible');
                    } else {
                        sticky.classList.remove('visible');
                    }
                }, {
                    passive: true
                });
            }
        });

        /* ─── Escape key closes modal ─── */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeRegisterModal();
        });
    </script>
</body>

</html>
<?php /**PATH /Users/teya2023/Downloads/invoice-saas-starter/resources/views/welcome.blade.php ENDPATH**/ ?>