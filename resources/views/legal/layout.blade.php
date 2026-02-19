<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'InvoiceSaaS — Pages légales')">
    <title>@yield('title') — InvoiceSaaS</title>
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

        /* ===== READING PROGRESS BAR ===== */
        #readingProgress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            z-index: 100;
            background: linear-gradient(90deg, #4f46e5, #6366f1, #818cf8);
            width: 0%;
            transition: width 50ms linear;
            box-shadow: 0 0 10px rgba(79, 70, 229, 0.5);
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

        .reveal-scale {
            opacity: 0;
            transform: scale(.96);
            transition: all .7s cubic-bezier(.16, 1, .3, 1)
        }

        .reveal-scale.visible {
            opacity: 1;
            transform: scale(1)
        }

        .stagger-children>* {
            opacity: 0;
            transform: translateY(20px);
            transition: all .5s cubic-bezier(.16, 1, .3, 1);
        }

        .stagger-children.visible>* {
            opacity: 1;
            transform: translateY(0)
        }

        .stagger-children.visible>*:nth-child(1) {
            transition-delay: 0ms
        }

        .stagger-children.visible>*:nth-child(2) {
            transition-delay: 60ms
        }

        .stagger-children.visible>*:nth-child(3) {
            transition-delay: 120ms
        }

        .stagger-children.visible>*:nth-child(4) {
            transition-delay: 180ms
        }

        .stagger-children.visible>*:nth-child(5) {
            transition-delay: 240ms
        }

        .stagger-children.visible>*:nth-child(6) {
            transition-delay: 300ms
        }

        /* ===== HERO GRADIENT ===== */
        .legal-hero {
            background:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(30, 58, 138, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 20%, rgba(79, 70, 229, 0.06) 0%, transparent 50%),
                radial-gradient(ellipse 40% 50% at 10% 60%, rgba(16, 185, 129, 0.04) 0%, transparent 50%);
        }

        .legal-hero-dots {
            background-image: radial-gradient(circle, rgba(30, 58, 138, .05) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .legal-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: orbFloat 12s ease-in-out infinite;
        }

        @keyframes orbFloat {

            0%,
            100% {
                transform: translateY(0) translateX(0)
            }

            33% {
                transform: translateY(-15px) translateX(10px)
            }

            66% {
                transform: translateY(10px) translateX(-8px)
            }
        }

        /* ===== TOC SIDEBAR ===== */
        .toc-link {
            position: relative;
            padding-left: 16px;
            border-left: 2px solid #e2e8f0;
        }

        .toc-link:hover,
        .toc-link.active {
            border-left-color: #4f46e5;
            color: #4f46e5;
        }

        .toc-link.active {
            font-weight: 600;
        }

        /* ===== ARTICLE SECTIONS ===== */
        .legal-section {
            scroll-margin-top: 100px;
            border-left: 3px solid transparent;
            padding-left: 24px;
            transition: border-color .3s ease;
        }

        .legal-section:hover {
            border-left-color: #e0e7ff;
        }

        .legal-section:target {
            border-left-color: #4f46e5;
        }

        /* ===== NAV SCROLL ===== */
        .nav-scrolled {
            background: rgba(255, 255, 255, .97);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
            backdrop-filter: blur(12px)
        }

        /* ===== SMOOTH BACK TO TOP ===== */
        .back-to-top {
            opacity: 0;
            transform: translateY(10px);
            transition: all .3s ease;
            pointer-events: none;
        }

        .back-to-top.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* ===== PROSE TABLE STYLING ===== */
        article table {
            border-collapse: collapse;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        }

        article table thead {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        }

        article table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #312e81;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        article table td {
            padding: 12px 16px;
            font-size: 14px;
            color: #475569;
            border-top: 1px solid #f1f5f9;
        }

        article table tbody tr:hover {
            background: #f8fafc;
        }

        article table tbody tr:nth-child(even) {
            background: #fafbfd;
        }

        /* ===== CALLOUT BOXES ===== */
        .callout-info {
            background: linear-gradient(135deg, #eef2ff, #f0f4ff);
            border-left: 4px solid #4f46e5;
            border-radius: 12px;
            padding: 20px 24px;
        }

        .callout-warning {
            background: linear-gradient(135deg, #fffbeb, #fef9ee);
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 20px 24px;
        }

        .callout-success {
            background: linear-gradient(135deg, #ecfdf5, #f0fdf9);
            border-left: 4px solid #10b981;
            border-radius: 12px;
            padding: 20px 24px;
        }
    </style>
</head>

<body class="bg-surface text-slate-900 antialiased">

    {{-- Reading Progress Bar --}}
    <div id="readingProgress"></div>

    {{-- Navbar --}}
    <nav id="mainNav"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/80 backdrop-blur-lg border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-extrabold tracking-tight text-slate-900">Invoice<span
                    class="text-brand-600">SaaS</span></a>
            <div class="hidden md:flex items-center gap-8">
                <a href="/#fonctionnalites"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Fonctionnalités</a>
                <a href="/#tarifs"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Tarifs</a>
                <a href="/#temoignages"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Témoignages</a>
                <a href="/#faq"
                    class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">FAQ</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="/login"
                    class="hidden sm:inline-flex text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors px-4 py-2">Connexion</a>
                <a href="/register"
                    class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all shadow-[0_1px_2px_rgba(0,0,0,.05),0_4px_12px_rgba(79,70,229,.25)] hover:shadow-[0_2px_4px_rgba(0,0,0,.05),0_8px_20px_rgba(79,70,229,.35)]">Essai
                    gratuit</a>
            </div>
        </div>
    </nav>

    {{-- Hero Header --}}
    <section class="relative pt-28 pb-12 sm:pt-32 sm:pb-16 overflow-hidden legal-hero">
        <div class="absolute inset-0 legal-hero-dots opacity-40"></div>
        <div class="legal-orb w-[300px] h-[300px] bg-brand-500/10 -top-[80px] -right-[60px] absolute"></div>
        <div class="legal-orb w-[200px] h-[200px] bg-accent-500/8 bottom-0 -left-[40px] absolute"
            style="animation-delay: 4s"></div>

        <div class="relative max-w-6xl mx-auto px-5 sm:px-6">
            {{-- Breadcrumb --}}
            <nav class="reveal flex items-center gap-2 text-sm text-slate-400 mb-6">
                <a href="/" class="hover:text-brand-600 transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                <span class="text-slate-600 font-medium">@yield('breadcrumb')</span>
            </nav>

            <div class="flex items-start gap-4 mb-4">
                {{-- Page Icon --}}
                <div
                    class="reveal hidden sm:flex w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 items-center justify-center flex-shrink-0 shadow-lg shadow-brand-500/20">
                    @yield('hero_icon', '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>')
                </div>
                <div>
                    <h1 class="reveal text-3xl sm:text-4xl lg:text-5xl font-extrabold text-deep-900 tracking-tight"
                        style="transition-delay:80ms">@yield('heading')</h1>
                </div>
            </div>

            <div class="reveal flex flex-wrap items-center gap-4 mt-4 sm:ml-[72px]" style="transition-delay:160ms">
                <span
                    class="inline-flex items-center gap-2 text-sm text-slate-400 bg-white/60 backdrop-blur-sm px-3.5 py-1.5 rounded-full border border-slate-100">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Mis à jour le @yield('updated_date', date('d/m/Y'))
                </span>
                <span
                    class="inline-flex items-center gap-2 text-sm text-slate-400 bg-white/60 backdrop-blur-sm px-3.5 py-1.5 rounded-full border border-slate-100">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span id="readTime">~5 min de lecture</span>
                </span>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <main class="py-12 sm:py-16">
        <div class="max-w-6xl mx-auto px-5 sm:px-6">
            <div class="flex gap-12">

                {{-- TOC Sidebar (Desktop) --}}
                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <div class="sticky top-28">
                        <div
                            class="reveal-scale bg-white rounded-2xl border border-slate-100 p-6 shadow-[0_1px_3px_rgba(0,0,0,.04)]">
                            <h3
                                class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                Sommaire
                            </h3>
                            <nav id="tocNav" class="flex flex-col gap-1 text-sm max-h-[60vh] overflow-y-auto">
                                {{-- Populated by JS --}}
                            </nav>
                        </div>

                        {{-- Quick nav to other legal pages --}}
                        <div
                            class="mt-6 bg-white rounded-2xl border border-slate-100 p-6 shadow-[0_1px_3px_rgba(0,0,0,.04)]">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Voir aussi</h3>
                            <div class="flex flex-col gap-2">
                                <a href="/conditions-generales"
                                    class="group flex items-center gap-2.5 p-2 rounded-lg hover:bg-brand-50/50 transition-all">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center group-hover:bg-brand-100 transition-colors flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-semibold text-slate-600 group-hover:text-brand-600 transition-colors">CGU</span>
                                </a>
                                <a href="/politique-confidentialite"
                                    class="group flex items-center gap-2.5 p-2 rounded-lg hover:bg-accent-50/50 transition-all">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-accent-50 flex items-center justify-center group-hover:bg-accent-100 transition-colors flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-accent-600" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-semibold text-slate-600 group-hover:text-brand-600 transition-colors">Confidentialité</span>
                                </a>
                                <a href="/mentions-legales"
                                    class="group flex items-center gap-2.5 p-2 rounded-lg hover:bg-violet-50/50 transition-all">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center group-hover:bg-violet-100 transition-colors flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-semibold text-slate-600 group-hover:text-brand-600 transition-colors">Mentions
                                        légales</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Main Content --}}
                <div class="flex-1 min-w-0">
                    <article id="legalArticle"
                        class="reveal bg-white rounded-3xl border border-slate-100 shadow-[0_1px_3px_rgba(0,0,0,.04)] p-6 sm:p-10 lg:p-12
                        prose prose-slate max-w-none
                        prose-headings:font-bold prose-headings:text-deep-900 prose-headings:tracking-tight
                        prose-h2:text-xl prose-h2:mt-12 prose-h2:mb-4 prose-h2:pb-3 prose-h2:border-b prose-h2:border-slate-100 prose-h2:scroll-mt-28
                        prose-h3:text-base prose-h3:mt-6 prose-h3:mb-2
                        prose-p:text-slate-500 prose-p:leading-relaxed prose-p:text-[15px]
                        prose-li:text-slate-500 prose-li:text-[15px] prose-li:leading-relaxed
                        prose-a:text-brand-600 prose-a:no-underline hover:prose-a:underline prose-a:font-medium
                        prose-strong:text-slate-700 prose-strong:font-semibold
                        prose-ul:space-y-1.5
                        prose-ol:space-y-1.5">
                        @yield('content')
                    </article>

                    {{-- Mobile-only: Navigation between legal pages --}}
                    <div class="lg:hidden mt-10 reveal">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Autres pages légales
                        </h4>
                        <div class="grid sm:grid-cols-3 gap-3 stagger-children">
                            <a href="/conditions-generales"
                                class="group flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-100 hover:border-brand-200 hover:bg-brand-50/30 hover:shadow-md transition-all">
                                <div
                                    class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-50 to-brand-100 flex items-center justify-center group-hover:from-brand-100 group-hover:to-brand-200 transition-colors">
                                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                                <div>
                                    <span
                                        class="text-sm font-semibold text-slate-700 group-hover:text-brand-600 transition-colors">CGU</span>
                                    <p class="text-xs text-slate-400 mt-0.5">Conditions d'utilisation</p>
                                </div>
                            </a>
                            <a href="/politique-confidentialite"
                                class="group flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-100 hover:border-brand-200 hover:bg-accent-50/30 hover:shadow-md transition-all">
                                <div
                                    class="w-9 h-9 rounded-lg bg-gradient-to-br from-accent-50 to-accent-100 flex items-center justify-center group-hover:from-accent-100 group-hover:to-accent-200 transition-colors">
                                    <svg class="w-4 h-4 text-accent-600" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                </div>
                                <div>
                                    <span
                                        class="text-sm font-semibold text-slate-700 group-hover:text-brand-600 transition-colors">Confidentialité</span>
                                    <p class="text-xs text-slate-400 mt-0.5">Protection des données</p>
                                </div>
                            </a>
                            <a href="/mentions-legales"
                                class="group flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-100 hover:border-brand-200 hover:bg-violet-50/30 hover:shadow-md transition-all">
                                <div
                                    class="w-9 h-9 rounded-lg bg-gradient-to-br from-violet-50 to-violet-100 flex items-center justify-center group-hover:from-violet-100 group-hover:to-violet-200 transition-colors">
                                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                </div>
                                <div>
                                    <span
                                        class="text-sm font-semibold text-slate-700 group-hover:text-brand-600 transition-colors">Mentions
                                        légales</span>
                                    <p class="text-xs text-slate-400 mt-0.5">Informations légales</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Back to Top Button --}}
    <button id="backToTop"
        class="back-to-top fixed bottom-8 right-8 z-40 w-11 h-11 rounded-xl bg-white border border-slate-200 shadow-lg flex items-center justify-center hover:bg-brand-50 hover:border-brand-200 transition-all cursor-pointer group"
        onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 transition-colors" fill="none"
            stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg>
    </button>

    {{-- Footer --}}
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
                        <li><a href="/#faq"
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
                <p class="text-xs text-slate-500">&copy; {{ date('Y') }} InvoiceSaaS. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== READING PROGRESS BAR =====
            const progressBar = document.getElementById('readingProgress');

            function updateProgress() {
                const scroll = window.scrollY;
                const height = document.documentElement.scrollHeight - window.innerHeight;
                const progress = height > 0 ? (scroll / height) * 100 : 0;
                progressBar.style.width = progress + '%';
            }
            window.addEventListener('scroll', updateProgress, {
                passive: true
            });

            // ===== TABLE OF CONTENTS =====
            const article = document.getElementById('legalArticle');
            const tocNav = document.getElementById('tocNav');
            if (article && tocNav) {
                const headings = article.querySelectorAll('h2');
                headings.forEach((h, i) => {
                    const id = 'section-' + i;
                    h.id = id;
                    const link = document.createElement('a');
                    link.href = '#' + id;
                    link.className =
                        'toc-link py-1.5 text-slate-500 hover:text-brand-600 transition-all text-[13px] leading-snug';
                    link.textContent = h.textContent;
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.getElementById(id).scrollIntoView({
                            behavior: 'smooth'
                        });
                    });
                    tocNav.appendChild(link);
                });

                // Active section tracking
                function updateActiveToc() {
                    let current = '';
                    headings.forEach((h, i) => {
                        if (h.getBoundingClientRect().top <= 120) {
                            current = 'section-' + i;
                        }
                    });
                    tocNav.querySelectorAll('.toc-link').forEach(link => {
                        link.classList.toggle('active', link.getAttribute('href') === '#' + current);
                    });
                }
                window.addEventListener('scroll', updateActiveToc, {
                    passive: true
                });
                updateActiveToc();
            }

            // ===== READING TIME =====
            if (article) {
                const text = article.textContent || '';
                const words = text.trim().split(/\s+/).length;
                const minutes = Math.max(1, Math.ceil(words / 200));
                const readTimeEl = document.getElementById('readTime');
                if (readTimeEl) readTimeEl.textContent = '~' + minutes + ' min de lecture';
            }

            // ===== SCROLL REVEAL ANIMATIONS =====
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -40px 0px'
            });

            document.querySelectorAll('.reveal, .reveal-left, .reveal-scale, .stagger-children').forEach(el => {
                observer.observe(el);
            });

            // ===== BACK TO TOP BUTTON =====
            const backToTop = document.getElementById('backToTop');
            window.addEventListener('scroll', function() {
                backToTop.classList.toggle('show', window.scrollY > 500);
            }, {
                passive: true
            });

            // ===== NAV SCROLL EFFECT =====
            const nav = document.getElementById('mainNav');
            window.addEventListener('scroll', function() {
                nav.classList.toggle('nav-scrolled', window.scrollY > 20);
            }, {
                passive: true
            });

        });
    </script>

</body>

</html>
