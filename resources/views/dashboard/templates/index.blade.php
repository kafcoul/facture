@extends('layouts.dashboard')

@section('title', 'Modèles de factures')

@section('content')
    <div x-data="{
        showPreview: false,
        previewTemplate: null,
        showUpgradeModal: false,
        requiredPlan: ''
    }" class="space-y-6">

        @php
            $user = auth()->user();
            $userPlan = $user->plan ?? 'starter';
            $planNames = ['starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'];
            $currentTemplate = $user->invoice_template ?? 'classic';
        @endphp

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modèles de factures</h1>
                <p class="mt-1 text-gray-600">Choisissez le design parfait pour vos factures</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    Template actuel : {{ $templates[$currentTemplate]['name'] ?? 'Classique' }}
                </span>
            </div>
        </div>

        <!-- Plan Info -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Votre plan : {{ $planNames[$userPlan] }}</p>
                        <p class="text-sm text-gray-600">
                            @if ($userPlan === 'starter')
                                2 templates disponibles - Passez au Pro pour plus de choix
                            @elseif($userPlan === 'pro')
                                5 templates disponibles - Passez à Enterprise pour tous les templates
                            @else
                                Accès à tous les templates premium
                            @endif
                        </p>
                    </div>
                </div>
                @if ($userPlan !== 'enterprise')
                    <a href="{{ route('client.settings.index') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-700">
                        Mettre à niveau →
                    </a>
                @endif
            </div>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($templates as $template)
                @php
                    $planHierarchy = ['starter' => 1, 'pro' => 2, 'enterprise' => 3];
                    $userPlanLevel = $planHierarchy[$userPlan] ?? 1;
                    $templatePlanLevel = $planHierarchy[$template['plan']] ?? 1;
                    $canUse = $userPlanLevel >= $templatePlanLevel;
                    $isActive = $currentTemplate === $template['id'];
                @endphp

                <div class="relative group">
                    <div
                        class="bg-white rounded-xl shadow-sm border-2 overflow-hidden transition-all duration-300 
                {{ $isActive ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 hover:shadow-md' }}
                {{ !$canUse ? 'opacity-75' : '' }}">

                        <!-- Template Preview -->
                        <div
                            class="aspect-[3/4] relative overflow-hidden
                    @if ($template['id'] === 'classic') bg-gradient-to-br from-blue-50 to-white
                    @elseif($template['id'] === 'modern') bg-gradient-to-br from-purple-100 via-fuchsia-50 to-indigo-100
                    @elseif($template['id'] === 'minimal') bg-white
                    @elseif($template['id'] === 'corporate') bg-gradient-to-b from-teal-800 to-teal-900
                    @elseif($template['id'] === 'creative') bg-gradient-to-br from-pink-400 via-rose-300 to-orange-300
                    @elseif($template['id'] === 'elegant') bg-gradient-to-b from-amber-50 via-yellow-50 to-orange-50
                    @elseif($template['id'] === 'premium') bg-gradient-to-br from-gray-900 via-gray-800 to-black
                    @elseif($template['id'] === 'african') bg-gradient-to-br from-red-600 via-yellow-500 to-green-600
                    @else bg-gradient-to-br from-gray-50 to-gray-100 @endif
                ">
                            {{-- ═══ CLASSIC: Clean blue header, white body, professional ═══ --}}
                            @if ($template['id'] === 'classic')
                                <div
                                    class="absolute inset-4 bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform group-hover:scale-105">
                                    <div class="h-10 bg-blue-600 flex items-center px-3">
                                        <div class="w-5 h-5 bg-white/30 rounded"></div>
                                        <div class="ml-auto flex space-x-1">
                                            <div class="h-1.5 w-8 bg-white/50 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <div class="flex justify-between mb-3">
                                            <div>
                                                <div class="h-2 w-14 bg-blue-200 rounded mb-1"></div>
                                                <div class="h-1.5 w-10 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="text-right">
                                                <div class="h-2.5 w-16 bg-blue-600 rounded mb-1"></div>
                                                <div class="h-1.5 w-12 bg-gray-200 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="h-px bg-blue-200 mb-2"></div>
                                        <div class="space-y-1.5 mb-3">
                                            <div class="flex justify-between">
                                                <div class="h-1.5 w-2/5 bg-gray-200 rounded"></div>
                                                <div class="h-1.5 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="flex justify-between">
                                                <div class="h-1.5 w-1/3 bg-gray-200 rounded"></div>
                                                <div class="h-1.5 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="flex justify-between">
                                                <div class="h-1.5 w-2/5 bg-gray-200 rounded"></div>
                                                <div class="h-1.5 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="h-px bg-blue-200 mb-2"></div>
                                        <div class="flex justify-end">
                                            <div class="h-2.5 w-20 bg-blue-600 rounded"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ═══ MODERN: Gradient sidebar, split layout ═══ --}}
                            @elseif($template['id'] === 'modern')
                                <div
                                    class="absolute inset-4 bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform group-hover:scale-105 flex">
                                    <div
                                        class="w-1/3 bg-gradient-to-b from-purple-600 to-indigo-700 p-2 flex flex-col justify-between">
                                        <div>
                                            <div class="w-6 h-6 bg-white/20 rounded-lg mb-3"></div>
                                            <div class="h-1.5 w-full bg-white/30 rounded mb-1"></div>
                                            <div class="h-1 w-3/4 bg-white/20 rounded mb-1"></div>
                                            <div class="h-1 w-2/3 bg-white/20 rounded"></div>
                                        </div>
                                        <div>
                                            <div class="h-1 w-full bg-white/20 rounded mb-1"></div>
                                            <div class="h-1 w-3/4 bg-white/20 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 p-2.5">
                                        <div class="h-3 w-16 bg-purple-200 rounded mb-2"></div>
                                        <div class="space-y-1 mb-2">
                                            <div class="h-1.5 w-full bg-gray-100 rounded"></div>
                                            <div class="h-1.5 w-3/4 bg-gray-100 rounded"></div>
                                        </div>
                                        <div class="space-y-1.5 mb-2">
                                            <div class="flex justify-between items-center p-1 bg-purple-50 rounded">
                                                <div class="h-1 w-1/2 bg-purple-200 rounded"></div>
                                                <div class="h-1 w-1/6 bg-purple-300 rounded"></div>
                                            </div>
                                            <div class="flex justify-between items-center p-1">
                                                <div class="h-1 w-2/5 bg-gray-200 rounded"></div>
                                                <div class="h-1 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="flex justify-between items-center p-1 bg-purple-50 rounded">
                                                <div class="h-1 w-1/2 bg-purple-200 rounded"></div>
                                                <div class="h-1 w-1/6 bg-purple-300 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="h-px bg-purple-200 mb-1"></div>
                                        <div class="flex justify-end">
                                            <div class="h-2 w-14 bg-gradient-to-r from-purple-500 to-indigo-600 rounded">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ═══ MINIMAL: Black & white, lots of space, clean ═══ --}}
                            @elseif($template['id'] === 'minimal')
                                <div
                                    class="absolute inset-4 bg-white rounded-lg shadow-lg p-4 transform transition-transform group-hover:scale-105 border border-gray-100">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="h-3 w-20 bg-gray-900 rounded"></div>
                                        <div class="h-1.5 w-10 bg-gray-300 rounded"></div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="h-1 w-16 bg-gray-200 rounded mb-1"></div>
                                        <div class="h-1 w-12 bg-gray-200 rounded"></div>
                                    </div>
                                    <div class="space-y-3 mb-4">
                                        <div class="flex justify-between border-b border-gray-100 pb-2">
                                            <div class="h-1 w-2/5 bg-gray-300 rounded"></div>
                                            <div class="h-1 w-1/6 bg-gray-300 rounded"></div>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-100 pb-2">
                                            <div class="h-1 w-1/3 bg-gray-300 rounded"></div>
                                            <div class="h-1 w-1/6 bg-gray-300 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-auto">
                                        <div class="border-t-2 border-gray-900 pt-1">
                                            <div class="h-2 w-16 bg-gray-900 rounded"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ═══ CORPORATE: Dark teal header, formal columns ═══ --}}
                            @elseif($template['id'] === 'corporate')
                                <div
                                    class="absolute inset-4 bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform group-hover:scale-105">
                                    <div
                                        class="h-14 bg-gradient-to-r from-teal-700 to-teal-600 p-2.5 flex items-end justify-between">
                                        <div>
                                            <div class="h-2 w-16 bg-white/80 rounded mb-0.5"></div>
                                            <div class="h-1 w-10 bg-white/40 rounded"></div>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <div class="w-5 h-5 bg-white/20 rounded-sm"></div>
                                        </div>
                                    </div>
                                    <div class="p-2.5">
                                        <div class="grid grid-cols-2 gap-2 mb-2">
                                            <div class="p-1.5 bg-teal-50 rounded">
                                                <div class="h-1 w-full bg-teal-200 rounded mb-0.5"></div>
                                                <div class="h-1 w-3/4 bg-teal-100 rounded"></div>
                                            </div>
                                            <div class="p-1.5 bg-teal-50 rounded">
                                                <div class="h-1 w-full bg-teal-200 rounded mb-0.5"></div>
                                                <div class="h-1 w-2/3 bg-teal-100 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="bg-teal-700 rounded-t px-1.5 py-0.5 flex justify-between">
                                            <div class="h-1 w-1/3 bg-white/60 rounded"></div>
                                            <div class="h-1 w-1/6 bg-white/60 rounded"></div>
                                        </div>
                                        <div class="border border-teal-100 rounded-b">
                                            <div class="flex justify-between p-1 border-b border-teal-50">
                                                <div class="h-1 w-2/5 bg-gray-200 rounded"></div>
                                                <div class="h-1 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="flex justify-between p-1 border-b border-teal-50">
                                                <div class="h-1 w-1/3 bg-gray-200 rounded"></div>
                                                <div class="h-1 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="flex justify-between p-1">
                                                <div class="h-1 w-2/5 bg-gray-200 rounded"></div>
                                                <div class="h-1 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end mt-1.5">
                                            <div class="px-2 py-1 bg-teal-700 rounded">
                                                <div class="h-1.5 w-14 bg-white/80 rounded"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ═══ CREATIVE: Bold colors, rounded shapes, playful ═══ --}}
                            @elseif($template['id'] === 'creative')
                                <div
                                    class="absolute inset-4 bg-white rounded-2xl shadow-lg overflow-hidden transform transition-transform group-hover:scale-105">
                                    <div class="p-3 relative">
                                        <div
                                            class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-pink-400 to-transparent rounded-bl-full opacity-60">
                                        </div>
                                        <div
                                            class="absolute bottom-0 left-0 w-12 h-12 bg-gradient-to-tr from-orange-300 to-transparent rounded-tr-full opacity-40">
                                        </div>
                                        <div class="relative">
                                            <div class="flex items-center mb-3">
                                                <div
                                                    class="w-7 h-7 bg-gradient-to-br from-pink-500 to-rose-600 rounded-full">
                                                </div>
                                                <div class="ml-2">
                                                    <div class="h-2 w-14 bg-pink-300 rounded-full"></div>
                                                </div>
                                                <div class="ml-auto">
                                                    <div
                                                        class="h-2.5 w-12 bg-gradient-to-r from-pink-500 to-orange-400 rounded-full">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 mb-2.5">
                                                <div class="flex-1 p-1.5 bg-pink-50 rounded-lg">
                                                    <div class="h-1 w-3/4 bg-pink-200 rounded-full mb-0.5"></div>
                                                    <div class="h-1 w-1/2 bg-pink-100 rounded-full"></div>
                                                </div>
                                                <div class="flex-1 p-1.5 bg-orange-50 rounded-lg">
                                                    <div class="h-1 w-3/4 bg-orange-200 rounded-full mb-0.5"></div>
                                                    <div class="h-1 w-1/2 bg-orange-100 rounded-full"></div>
                                                </div>
                                            </div>
                                            <div class="space-y-1 mb-2">
                                                <div
                                                    class="flex justify-between items-center p-1 bg-gradient-to-r from-pink-50 to-transparent rounded-lg">
                                                    <div class="h-1 w-2/5 bg-pink-200 rounded-full"></div>
                                                    <div class="h-1 w-1/6 bg-pink-300 rounded-full"></div>
                                                </div>
                                                <div class="flex justify-between items-center p-1">
                                                    <div class="h-1 w-1/3 bg-gray-200 rounded-full"></div>
                                                    <div class="h-1 w-1/6 bg-gray-200 rounded-full"></div>
                                                </div>
                                                <div
                                                    class="flex justify-between items-center p-1 bg-gradient-to-r from-orange-50 to-transparent rounded-lg">
                                                    <div class="h-1 w-2/5 bg-orange-200 rounded-full"></div>
                                                    <div class="h-1 w-1/6 bg-orange-300 rounded-full"></div>
                                                </div>
                                            </div>
                                            <div class="flex justify-end">
                                                <div
                                                    class="px-2 py-1 bg-gradient-to-r from-pink-500 to-orange-400 rounded-full">
                                                    <div class="h-1.5 w-10 bg-white/70 rounded"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ═══ ELEGANT: Gold accents, serif feel, border ornamentation ═══ --}}
                            @elseif($template['id'] === 'elegant')
                                <div
                                    class="absolute inset-4 bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform group-hover:scale-105 border border-amber-200">
                                    <div class="h-1 bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-300"></div>
                                    <div class="p-3">
                                        <div class="text-center mb-3 pb-2 border-b border-amber-200">
                                            <div class="h-3 w-20 bg-amber-700 rounded mx-auto mb-1"></div>
                                            <div class="flex justify-center space-x-1">
                                                <div class="w-1 h-1 bg-amber-400 rounded-full"></div>
                                                <div class="w-6 h-px bg-amber-300 mt-0.5"></div>
                                                <div class="w-1 h-1 bg-amber-400 rounded-full"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between mb-3">
                                            <div>
                                                <div class="h-1 w-14 bg-amber-200 rounded mb-0.5"></div>
                                                <div class="h-1 w-10 bg-amber-100 rounded"></div>
                                            </div>
                                            <div class="text-right">
                                                <div class="h-1.5 w-12 bg-amber-600 rounded mb-0.5"></div>
                                                <div class="h-1 w-10 bg-amber-200 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="border border-amber-200 rounded overflow-hidden mb-2">
                                            <div class="bg-amber-50 px-1.5 py-0.5 flex justify-between">
                                                <div class="h-1 w-1/3 bg-amber-400 rounded"></div>
                                                <div class="h-1 w-1/6 bg-amber-400 rounded"></div>
                                            </div>
                                            <div class="px-1.5 py-1 flex justify-between border-t border-amber-100">
                                                <div class="h-1 w-2/5 bg-gray-200 rounded"></div>
                                                <div class="h-1 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="px-1.5 py-1 flex justify-between border-t border-amber-100">
                                                <div class="h-1 w-1/3 bg-gray-200 rounded"></div>
                                                <div class="h-1 w-1/6 bg-gray-200 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end">
                                            <div class="border-t-2 border-amber-500 pt-1">
                                                <div class="h-2 w-16 bg-amber-600 rounded"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="h-1 bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-300"></div>
                                </div>

                                {{-- ═══ PREMIUM: Black background, gold text, luxury feel ═══ --}}
                            @elseif($template['id'] === 'premium')
                                <div
                                    class="absolute inset-4 bg-gray-900 rounded-lg shadow-lg overflow-hidden transform transition-transform group-hover:scale-105 border border-amber-500/30">
                                    <div class="h-0.5 bg-gradient-to-r from-transparent via-amber-400 to-transparent">
                                    </div>
                                    <div class="p-3">
                                        <div class="flex justify-between items-start mb-3">
                                            <div
                                                class="w-7 h-7 bg-gradient-to-br from-amber-400 to-amber-600 rounded flex items-center justify-center">
                                                <div class="w-3 h-3 border border-amber-900 rounded-sm"></div>
                                            </div>
                                            <div class="text-right">
                                                <div
                                                    class="h-2.5 w-16 bg-gradient-to-r from-amber-400 to-amber-500 rounded">
                                                </div>
                                                <div class="h-1 w-10 bg-gray-600 rounded mt-1"></div>
                                            </div>
                                        </div>
                                        <div class="mb-2.5 pb-2 border-b border-gray-700">
                                            <div class="h-1 w-16 bg-gray-600 rounded mb-0.5"></div>
                                            <div class="h-1 w-12 bg-gray-700 rounded"></div>
                                        </div>
                                        <div class="space-y-1.5 mb-2.5">
                                            <div class="flex justify-between">
                                                <div class="h-1 w-2/5 bg-gray-600 rounded"></div>
                                                <div class="h-1 w-1/6 bg-amber-500/50 rounded"></div>
                                            </div>
                                            <div class="flex justify-between">
                                                <div class="h-1 w-1/3 bg-gray-600 rounded"></div>
                                                <div class="h-1 w-1/6 bg-amber-500/50 rounded"></div>
                                            </div>
                                            <div class="flex justify-between">
                                                <div class="h-1 w-2/5 bg-gray-600 rounded"></div>
                                                <div class="h-1 w-1/6 bg-amber-500/50 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="border-t border-amber-500/40 pt-1.5 flex justify-end">
                                            <div class="h-2.5 w-20 bg-gradient-to-r from-amber-400 to-amber-600 rounded">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="h-0.5 bg-gradient-to-r from-transparent via-amber-400 to-transparent">
                                    </div>
                                </div>

                                {{-- ═══ AFRICAN: Bold patterns, red+green+gold, geometric motifs ═══ --}}
                            @elseif($template['id'] === 'african')
                                <div
                                    class="absolute inset-4 bg-amber-50 rounded-lg shadow-lg overflow-hidden transform transition-transform group-hover:scale-105">
                                    <div
                                        class="h-3 bg-gradient-to-r from-red-600 via-yellow-500 to-green-600 flex items-center justify-center">
                                        <div class="flex space-x-0.5">
                                            @for ($i = 0; $i < 8; $i++)
                                                <div class="w-1 h-1 bg-white/40 rounded-full"></div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-1.5 bg-gradient-to-b from-red-600 via-yellow-500 to-green-600"></div>
                                        <div class="flex-1 p-2.5">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex items-center space-x-1.5">
                                                    <div
                                                        class="w-6 h-6 bg-gradient-to-br from-red-500 to-green-600 rounded-full flex items-center justify-center">
                                                        <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                                                    </div>
                                                    <div>
                                                        <div class="h-1.5 w-12 bg-red-400 rounded"></div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="h-2 w-14 bg-green-700 rounded"></div>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 mb-2">
                                                <div class="flex-1 p-1 bg-red-50 border border-red-200 rounded">
                                                    <div class="h-1 w-full bg-red-200 rounded mb-0.5"></div>
                                                    <div class="h-1 w-2/3 bg-red-100 rounded"></div>
                                                </div>
                                                <div class="flex-1 p-1 bg-green-50 border border-green-200 rounded">
                                                    <div class="h-1 w-full bg-green-200 rounded mb-0.5"></div>
                                                    <div class="h-1 w-2/3 bg-green-100 rounded"></div>
                                                </div>
                                            </div>
                                            <div class="space-y-1 mb-2">
                                                <div class="flex justify-between p-0.5 bg-yellow-50 rounded">
                                                    <div class="h-1 w-2/5 bg-yellow-300 rounded"></div>
                                                    <div class="h-1 w-1/6 bg-yellow-400 rounded"></div>
                                                </div>
                                                <div class="flex justify-between p-0.5">
                                                    <div class="h-1 w-1/3 bg-gray-300 rounded"></div>
                                                    <div class="h-1 w-1/6 bg-gray-300 rounded"></div>
                                                </div>
                                                <div class="flex justify-between p-0.5 bg-yellow-50 rounded">
                                                    <div class="h-1 w-2/5 bg-yellow-300 rounded"></div>
                                                    <div class="h-1 w-1/6 bg-yellow-400 rounded"></div>
                                                </div>
                                            </div>
                                            <div class="flex justify-end">
                                                <div
                                                    class="px-1.5 py-0.5 bg-gradient-to-r from-red-600 to-green-600 rounded">
                                                    <div class="h-1.5 w-12 bg-white/60 rounded"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-1.5 bg-gradient-to-b from-green-600 via-yellow-500 to-red-600"></div>
                                    </div>
                                    <div
                                        class="h-3 bg-gradient-to-r from-green-600 via-yellow-500 to-red-600 flex items-center justify-center">
                                        <div class="flex space-x-0.5">
                                            @for ($i = 0; $i < 8; $i++)
                                                <div class="w-1 h-1 bg-white/40 rounded-full"></div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Lock Overlay for Locked Templates -->
                            @if (!$canUse)
                                <div class="absolute inset-0 bg-gray-900/40 flex items-center justify-center">
                                    <div class="bg-white rounded-full p-3 shadow-lg">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>
                            @endif

                            <!-- Active Badge -->
                            @if ($isActive)
                                <div class="absolute top-2 right-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500 text-white shadow">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Actif
                                    </span>
                                </div>
                            @endif

                            <!-- Plan Badge -->
                            @if ($template['plan'] !== 'starter')
                                <div class="absolute top-2 left-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium shadow
                            {{ $template['plan'] === 'pro' ? 'bg-purple-500 text-white' : 'bg-gradient-to-r from-amber-500 to-orange-500 text-white' }}">
                                        {{ strtoupper($template['plan']) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Template Info -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $template['name'] }}</h3>
                                    <p class="text-sm text-gray-600">{{ $template['description'] }}</p>
                                </div>
                                <!-- Color Preview -->
                                <div class="flex -space-x-1">
                                    <div class="w-4 h-4 rounded-full border-2 border-white shadow"
                                        style="background-color: {{ $template['colors']['primary'] }}"></div>
                                    <div class="w-4 h-4 rounded-full border-2 border-white shadow"
                                        style="background-color: {{ $template['colors']['secondary'] }}"></div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2 mt-3">
                                @if ($canUse)
                                    @if ($isActive)
                                        <span
                                            class="flex-1 text-center py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium">
                                            Template actuel
                                        </span>
                                    @else
                                        <form action="{{ route('client.templates.select', $template['id']) }}"
                                            method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                class="w-full py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                                Utiliser
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button"
                                        @click="showPreview = true; previewTemplate = {{ json_encode($template) }}"
                                        class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="button"
                                        @click="showUpgradeModal = true; requiredPlan = '{{ $planNames[$template['plan']] }}'"
                                        class="flex-1 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg text-sm font-medium hover:from-purple-600 hover:to-pink-600 transition-colors">
                                        Débloquer
                                    </button>
                                    <button type="button"
                                        @click="showPreview = true; previewTemplate = {{ json_encode($template) }}"
                                        class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Preview Modal -->
        <div x-show="showPreview" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-gray-900/75" @click="showPreview = false"></div>

                <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
                    x-transition>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" x-text="previewTemplate?.name"></h3>
                            <p class="text-sm text-gray-600" x-text="previewTemplate?.description"></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-500">Couleurs:</span>
                                <div class="w-5 h-5 rounded-full border-2 border-white shadow"
                                    :style="'background-color:' + previewTemplate?.colors?.primary"></div>
                                <div class="w-5 h-5 rounded-full border-2 border-white shadow"
                                    :style="'background-color:' + previewTemplate?.colors?.secondary"></div>
                            </div>
                            <button @click="showPreview = false"
                                class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                        <div class="mx-auto max-w-2xl shadow-2xl rounded-lg overflow-hidden"
                            style="aspect-ratio: 1/1.414;">

                            {{-- ═══════════ CLASSIC PREVIEW ═══════════ --}}
                            <template x-if="previewTemplate?.id === 'classic'">
                                <div class="h-full flex flex-col bg-white">
                                    <div class="h-2 w-full"
                                        :style="'background-color:' + previewTemplate?.colors?.primary"></div>
                                    <div class="p-8 flex-1 flex flex-col">
                                        <div class="flex justify-between items-start mb-8">
                                            <div>
                                                <div class="w-14 h-14 rounded-lg mb-3 flex items-center justify-center text-white font-bold"
                                                    :style="'background-color:' + previewTemplate?.colors?.primary">LOGO
                                                </div>
                                                <p class="font-bold text-gray-900 text-sm">Votre Entreprise</p>
                                                <p class="text-xs text-gray-500">123 Rue du Commerce, Dakar</p>
                                                <p class="text-xs text-gray-500">Tel: +221 77 123 45 67</p>
                                            </div>
                                            <div class="text-right">
                                                <h2 class="text-3xl font-bold tracking-tight"
                                                    :style="'color:' + previewTemplate?.colors?.primary">FACTURE</h2>
                                                <p class="text-gray-600 mt-1 text-sm">N° <span
                                                        class="font-mono">INV-2025-001</span></p>
                                                <div class="mt-3 text-xs text-gray-500 space-y-0.5">
                                                    <p>Date: 20/02/2026</p>
                                                    <p>Échéance: 22/03/2026</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-6 p-4 rounded-lg border"
                                            :style="'border-color:' + previewTemplate?.colors?.primary +
                                                '40; background-color:' + previewTemplate?.colors?.primary + '08'">
                                            <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                                                Facturer à</p>
                                            <p class="font-bold text-gray-900 text-sm">Client Exemple SARL</p>
                                            <p class="text-xs text-gray-600">456 Avenue des Affaires, Abidjan</p>
                                        </div>
                                        <table class="w-full mb-6 flex-1">
                                            <thead>
                                                <tr :style="'background-color:' + previewTemplate?.colors?.primary">
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-white">
                                                        Description</th>
                                                    <th class="text-center py-2 px-3 text-xs font-semibold text-white">Qté
                                                    </th>
                                                    <th class="text-right py-2 px-3 text-xs font-semibold text-white">Prix
                                                        unitaire</th>
                                                    <th class="text-right py-2 px-3 text-xs font-semibold text-white">Total
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-2.5 px-3 text-sm">Service de consultation</td>
                                                    <td class="py-2.5 px-3 text-sm text-center">10</td>
                                                    <td class="py-2.5 px-3 text-sm text-right">50 000</td>
                                                    <td class="py-2.5 px-3 text-sm text-right font-medium">500 000</td>
                                                </tr>
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-2.5 px-3 text-sm">Développement web</td>
                                                    <td class="py-2.5 px-3 text-sm text-center">1</td>
                                                    <td class="py-2.5 px-3 text-sm text-right">750 000</td>
                                                    <td class="py-2.5 px-3 text-sm text-right font-medium">750 000</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-2.5 px-3 text-sm">Maintenance annuelle</td>
                                                    <td class="py-2.5 px-3 text-sm text-center">1</td>
                                                    <td class="py-2.5 px-3 text-sm text-right">200 000</td>
                                                    <td class="py-2.5 px-3 text-sm text-right font-medium">200 000</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="flex justify-end">
                                            <div class="w-64">
                                                <div class="flex justify-between py-1 text-sm"><span
                                                        class="text-gray-500">Sous-total</span><span>1 450 000 XOF</span>
                                                </div>
                                                <div class="flex justify-between py-1 text-sm"><span
                                                        class="text-gray-500">TVA (18%)</span><span>261 000 XOF</span>
                                                </div>
                                                <div class="flex justify-between py-2 border-t-2 mt-1"
                                                    :style="'border-color:' + previewTemplate?.colors?.primary"><span
                                                        class="font-bold">Total</span><span class="font-bold text-lg"
                                                        :style="'color:' + previewTemplate?.colors?.primary">1 711 000
                                                        XOF</span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="h-2 w-full"
                                        :style="'background-color:' + previewTemplate?.colors?.primary"></div>
                                </div>
                            </template>

                            {{-- ═══════════ MODERN PREVIEW — Sidebar layout ═══════════ --}}
                            <template x-if="previewTemplate?.id === 'modern'">
                                <div class="h-full flex bg-white">
                                    <div class="w-1/3 p-6 flex flex-col justify-between text-white"
                                        :style="'background: linear-gradient(to bottom,' + previewTemplate?.colors?.primary +
                                            ',' + previewTemplate?.colors?.secondary + ')'">
                                        <div>
                                            <div
                                                class="w-12 h-12 bg-white/20 rounded-xl mb-4 flex items-center justify-center font-bold text-lg">
                                                VE</div>
                                            <h3 class="font-bold text-sm mb-1">Votre Entreprise</h3>
                                            <p class="text-xs text-white/70">123 Rue du Commerce</p>
                                            <p class="text-xs text-white/70">Dakar, Sénégal</p>
                                            <p class="text-xs text-white/70 mt-2">contact@entreprise.com</p>
                                            <p class="text-xs text-white/70">+221 77 123 45 67</p>
                                        </div>
                                        <div>
                                            <div class="h-px bg-white/20 mb-3"></div>
                                            <p class="text-xs text-white/50 mb-1">Facturer à</p>
                                            <p class="font-semibold text-sm">Client Exemple SARL</p>
                                            <p class="text-xs text-white/70">456 Avenue des Affaires</p>
                                            <p class="text-xs text-white/70">Abidjan, Côte d'Ivoire</p>
                                        </div>
                                    </div>
                                    <div class="flex-1 p-6 flex flex-col">
                                        <div class="flex justify-between items-start mb-6">
                                            <div>
                                                <h2 class="text-2xl font-black tracking-tight"
                                                    :style="'color:' + previewTemplate?.colors?.primary">FACTURE</h2>
                                                <p class="text-xs text-gray-400 font-mono mt-1">INV-2025-001</p>
                                            </div>
                                            <div class="text-right text-xs text-gray-500">
                                                <p>Date: 20/02/2026</p>
                                                <p>Échéance: 22/03/2026</p>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="rounded-lg overflow-hidden border border-gray-100">
                                                <div class="grid grid-cols-12 text-xs font-semibold text-white py-2 px-3"
                                                    :style="'background-color:' + previewTemplate?.colors?.primary"><span
                                                        class="col-span-5">Description</span><span
                                                        class="col-span-2 text-center">Qté</span><span
                                                        class="col-span-2 text-right">P.U.</span><span
                                                        class="col-span-3 text-right">Total</span></div>
                                                <div class="divide-y divide-gray-50">
                                                    <div class="grid grid-cols-12 text-xs py-2.5 px-3"><span
                                                            class="col-span-5">Consultation</span><span
                                                            class="col-span-2 text-center text-gray-500">10</span><span
                                                            class="col-span-2 text-right text-gray-500">50 000</span><span
                                                            class="col-span-3 text-right font-medium">500 000</span></div>
                                                    <div class="grid grid-cols-12 text-xs py-2.5 px-3 bg-gray-50/50"><span
                                                            class="col-span-5">Développement web</span><span
                                                            class="col-span-2 text-center text-gray-500">1</span><span
                                                            class="col-span-2 text-right text-gray-500">750 000</span><span
                                                            class="col-span-3 text-right font-medium">750 000</span></div>
                                                    <div class="grid grid-cols-12 text-xs py-2.5 px-3"><span
                                                            class="col-span-5">Maintenance</span><span
                                                            class="col-span-2 text-center text-gray-500">1</span><span
                                                            class="col-span-2 text-right text-gray-500">200 000</span><span
                                                            class="col-span-3 text-right font-medium">200 000</span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end mt-4">
                                            <div class="w-56">
                                                <div class="flex justify-between py-1 text-xs"><span
                                                        class="text-gray-400">Sous-total</span><span>1 450 000</span></div>
                                                <div class="flex justify-between py-1 text-xs"><span
                                                        class="text-gray-400">TVA 18%</span><span>261 000</span></div>
                                                <div class="flex justify-between py-2 border-t-2 mt-1"
                                                    :style="'border-color:' + previewTemplate?.colors?.primary"><span
                                                        class="font-bold text-sm">Total</span><span
                                                        class="font-bold text-base"
                                                        :style="'color:' + previewTemplate?.colors?.primary">1 711 000
                                                        XOF</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- ═══════════ MINIMAL PREVIEW — Ultra-clean ═══════════ --}}
                            <template x-if="previewTemplate?.id === 'minimal'">
                                <div class="h-full bg-white p-10 flex flex-col">
                                    <div class="flex justify-between items-start mb-12">
                                        <h2 class="text-4xl font-black tracking-tighter text-gray-900">FACTURE</h2>
                                        <div class="text-right text-xs text-gray-400 space-y-0.5">
                                            <p class="font-mono text-sm text-gray-600">INV-2025-001</p>
                                            <p>20/02/2026</p>
                                            <p>Échéance: 22/03/2026</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-12 mb-10 text-xs">
                                        <div>
                                            <p class="uppercase tracking-widest text-gray-400 text-[10px] mb-2">De</p>
                                            <p class="text-sm font-semibold text-gray-900">Votre Entreprise</p>
                                            <p class="text-gray-500 mt-1">123 Rue du Commerce<br>Dakar, Sénégal</p>
                                        </div>
                                        <div>
                                            <p class="uppercase tracking-widest text-gray-400 text-[10px] mb-2">Pour</p>
                                            <p class="text-sm font-semibold text-gray-900">Client Exemple SARL</p>
                                            <p class="text-gray-500 mt-1">456 Avenue des Affaires<br>Abidjan, Côte d'Ivoire
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="border-t border-b border-gray-900 py-1.5 mb-2 flex text-[10px] uppercase tracking-widest text-gray-500 font-semibold">
                                            <span class="flex-1">Description</span><span
                                                class="w-16 text-center">Qté</span><span
                                                class="w-24 text-right">Prix</span><span
                                                class="w-24 text-right">Total</span></div>
                                        <div class="divide-y divide-gray-100 text-xs">
                                            <div class="flex py-3"><span class="flex-1">Service de
                                                    consultation</span><span
                                                    class="w-16 text-center text-gray-500">10</span><span
                                                    class="w-24 text-right text-gray-500">50 000</span><span
                                                    class="w-24 text-right">500 000</span></div>
                                            <div class="flex py-3"><span class="flex-1">Développement web</span><span
                                                    class="w-16 text-center text-gray-500">1</span><span
                                                    class="w-24 text-right text-gray-500">750 000</span><span
                                                    class="w-24 text-right">750 000</span></div>
                                            <div class="flex py-3"><span class="flex-1">Maintenance annuelle</span><span
                                                    class="w-16 text-center text-gray-500">1</span><span
                                                    class="w-24 text-right text-gray-500">200 000</span><span
                                                    class="w-24 text-right">200 000</span></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-6">
                                        <div class="w-56 text-xs">
                                            <div class="flex justify-between py-1"><span
                                                    class="text-gray-400">Sous-total</span><span>1 450 000</span></div>
                                            <div class="flex justify-between py-1"><span class="text-gray-400">TVA
                                                    18%</span><span>261 000</span></div>
                                            <div class="flex justify-between py-2 border-t-2 border-gray-900 mt-2"><span
                                                    class="font-black text-sm">TOTAL</span><span
                                                    class="font-black text-lg">1 711 000 XOF</span></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- ═══════════ CORPORATE PREVIEW — Dark header, formal ═══════════ --}}
                            <template x-if="previewTemplate?.id === 'corporate'">
                                <div class="h-full bg-white flex flex-col">
                                    <div class="px-8 py-6 text-white flex justify-between items-end"
                                        :style="'background: linear-gradient(135deg,' + previewTemplate?.colors?.primary + ',' +
                                            previewTemplate?.colors?.secondary + ')'">
                                        <div>
                                            <div
                                                class="w-10 h-10 bg-white/20 rounded mb-2 flex items-center justify-center text-xs font-bold">
                                                LOGO</div>
                                            <p class="font-bold">Votre Entreprise</p>
                                            <p class="text-xs text-white/70">123 Rue du Commerce, Dakar</p>
                                        </div>
                                        <div class="text-right">
                                            <h2 class="text-2xl font-bold tracking-wide">FACTURE</h2>
                                            <p class="text-xs text-white/70 font-mono mt-1">INV-2025-001</p>
                                        </div>
                                    </div>
                                    <div class="p-8 flex-1 flex flex-col">
                                        <div class="grid grid-cols-3 gap-4 mb-6">
                                            <div class="p-3 rounded-lg"
                                                :style="'background-color:' + previewTemplate?.colors?.primary +
                                                    '08; border: 1px solid ' + previewTemplate?.colors?.primary + '20'">
                                                <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">Client
                                                </p>
                                                <p class="text-xs font-semibold">Client Exemple SARL</p>
                                                <p class="text-xs text-gray-500">Abidjan, Côte d'Ivoire</p>
                                            </div>
                                            <div class="p-3 rounded-lg"
                                                :style="'background-color:' + previewTemplate?.colors?.primary +
                                                    '08; border: 1px solid ' + previewTemplate?.colors?.primary + '20'">
                                                <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">Date
                                                    d'émission</p>
                                                <p class="text-xs font-semibold">20/02/2026</p>
                                            </div>
                                            <div class="p-3 rounded-lg"
                                                :style="'background-color:' + previewTemplate?.colors?.primary +
                                                    '08; border: 1px solid ' + previewTemplate?.colors?.primary + '20'">
                                                <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">Date
                                                    d'échéance</p>
                                                <p class="text-xs font-semibold">22/03/2026</p>
                                            </div>
                                        </div>
                                        <div class="flex-1 rounded-lg overflow-hidden border border-gray-200">
                                            <div class="text-xs font-semibold text-white py-2 px-4 flex"
                                                :style="'background-color:' + previewTemplate?.colors?.primary"><span
                                                    class="flex-1">Description</span><span
                                                    class="w-12 text-center">Qté</span><span
                                                    class="w-20 text-right">P.U.</span><span class="w-24 text-right">Total
                                                    HT</span></div>
                                            <div class="text-xs divide-y divide-gray-100">
                                                <div class="flex py-2.5 px-4"><span class="flex-1">Service de
                                                        consultation</span><span
                                                        class="w-12 text-center text-gray-500">10</span><span
                                                        class="w-20 text-right text-gray-500">50 000</span><span
                                                        class="w-24 text-right font-medium">500 000</span></div>
                                                <div class="flex py-2.5 px-4 bg-gray-50/50"><span
                                                        class="flex-1">Développement web</span><span
                                                        class="w-12 text-center text-gray-500">1</span><span
                                                        class="w-20 text-right text-gray-500">750 000</span><span
                                                        class="w-24 text-right font-medium">750 000</span></div>
                                                <div class="flex py-2.5 px-4"><span class="flex-1">Maintenance
                                                        annuelle</span><span
                                                        class="w-12 text-center text-gray-500">1</span><span
                                                        class="w-20 text-right text-gray-500">200 000</span><span
                                                        class="w-24 text-right font-medium">200 000</span></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end mt-4">
                                            <div class="w-64 text-xs">
                                                <div class="flex justify-between py-1"><span
                                                        class="text-gray-400">Sous-total</span><span>1 450 000 XOF</span>
                                                </div>
                                                <div class="flex justify-between py-1"><span class="text-gray-400">TVA
                                                        18%</span><span>261 000 XOF</span></div>
                                                <div class="flex justify-between py-2.5 rounded-lg px-3 mt-1 text-white text-sm font-bold"
                                                    :style="'background-color:' + previewTemplate?.colors?.primary">
                                                    <span>Total TTC</span><span>1 711 000 XOF</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- ═══════════ CREATIVE PREVIEW — Rounded, bold colors ═══════════ --}}
                            <template x-if="previewTemplate?.id === 'creative'">
                                <div
                                    class="h-full bg-gradient-to-br from-pink-50 via-white to-orange-50 p-8 flex flex-col relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-40 h-40 rounded-full opacity-20"
                                        :style="'background-color:' + previewTemplate?.colors?.primary"></div>
                                    <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full opacity-10"
                                        :style="'background-color:' + previewTemplate?.colors?.secondary"></div>
                                    <div class="relative z-10 flex-1 flex flex-col">
                                        <div class="flex justify-between items-center mb-8">
                                            <div class="flex items-center gap-3">
                                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-bold"
                                                    :style="'background: linear-gradient(135deg,' + previewTemplate?.colors
                                                        ?.primary + ',' + previewTemplate?.colors?.secondary + ')'">
                                                    VE</div>
                                                <div>
                                                    <p class="font-bold text-gray-900">Votre Entreprise</p>
                                                    <p class="text-xs text-gray-500">Dakar, Sénégal</p>
                                                </div>
                                            </div>
                                            <div class="px-5 py-2 rounded-full text-white font-bold text-sm"
                                                :style="'background: linear-gradient(135deg,' + previewTemplate?.colors
                                                    ?.primary + ',' + previewTemplate?.colors?.secondary + ')'">
                                                FACTURE</div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 mb-6">
                                            <div class="p-4 bg-white rounded-2xl shadow-sm">
                                                <p class="text-[10px] uppercase tracking-widest text-gray-400 mb-1">
                                                    Facturer à</p>
                                                <p class="text-sm font-bold">Client Exemple SARL</p>
                                                <p class="text-xs text-gray-500">Abidjan, Côte d'Ivoire</p>
                                            </div>
                                            <div class="p-4 bg-white rounded-2xl shadow-sm">
                                                <p class="text-[10px] uppercase tracking-widest text-gray-400 mb-1">Détails
                                                </p>
                                                <p class="text-xs"><span
                                                        class="font-mono font-semibold">INV-2025-001</span></p>
                                                <p class="text-xs text-gray-500 mt-1">20/02/2026 → 22/03/2026</p>
                                            </div>
                                        </div>
                                        <div class="flex-1 bg-white rounded-2xl shadow-sm overflow-hidden">
                                            <div class="text-xs font-bold text-white py-2.5 px-4 flex rounded-t-2xl"
                                                :style="'background: linear-gradient(90deg,' + previewTemplate?.colors
                                                    ?.primary + ',' + previewTemplate?.colors?.secondary + ')'">
                                                <span class="flex-1">Description</span><span
                                                    class="w-12 text-center">Qté</span><span
                                                    class="w-20 text-right">Prix</span><span
                                                    class="w-24 text-right">Total</span></div>
                                            <div class="text-xs divide-y divide-gray-50">
                                                <div class="flex py-3 px-4"><span class="flex-1">Consultation</span><span
                                                        class="w-12 text-center text-gray-400">10</span><span
                                                        class="w-20 text-right text-gray-400">50 000</span><span
                                                        class="w-24 text-right font-semibold">500 000</span></div>
                                                <div class="flex py-3 px-4"><span class="flex-1">Développement
                                                        web</span><span
                                                        class="w-12 text-center text-gray-400">1</span><span
                                                        class="w-20 text-right text-gray-400">750 000</span><span
                                                        class="w-24 text-right font-semibold">750 000</span></div>
                                                <div class="flex py-3 px-4"><span class="flex-1">Maintenance</span><span
                                                        class="w-12 text-center text-gray-400">1</span><span
                                                        class="w-20 text-right text-gray-400">200 000</span><span
                                                        class="w-24 text-right font-semibold">200 000</span></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end mt-4">
                                            <div class="w-56 text-xs">
                                                <div class="flex justify-between py-1"><span
                                                        class="text-gray-400">Sous-total</span><span>1 450 000</span></div>
                                                <div class="flex justify-between py-1"><span class="text-gray-400">TVA
                                                        18%</span><span>261 000</span></div>
                                                <div class="mt-1 py-2.5 px-4 rounded-full text-white text-sm font-bold flex justify-between"
                                                    :style="'background: linear-gradient(90deg,' + previewTemplate?.colors
                                                        ?.primary + ',' + previewTemplate?.colors?.secondary + ')'">
                                                    <span>Total</span><span>1 711 000 XOF</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- ═══════════ ELEGANT PREVIEW — Gold borders, centered ═══════════ --}}
                            <template x-if="previewTemplate?.id === 'elegant'">
                                <div class="h-full bg-amber-50/30 flex flex-col">
                                    <div class="h-1.5"
                                        :style="'background: linear-gradient(90deg, transparent,' + previewTemplate?.colors
                                            ?.primary + ', transparent)'">
                                    </div>
                                    <div class="p-8 flex-1 flex flex-col border-x-2"
                                        :style="'border-color:' + previewTemplate?.colors?.primary + '30'">
                                        <div class="text-center mb-8 pb-4 border-b"
                                            :style="'border-color:' + previewTemplate?.colors?.primary + '40'">
                                            <h2 class="text-3xl font-bold tracking-wide"
                                                :style="'color:' + previewTemplate?.colors?.primary">FACTURE</h2>
                                            <div class="flex items-center justify-center gap-2 mt-2">
                                                <div class="w-8 h-px"
                                                    :style="'background-color:' + previewTemplate?.colors?.primary"></div>
                                                <div class="w-2 h-2 rounded-full"
                                                    :style="'background-color:' + previewTemplate?.colors?.primary"></div>
                                                <div class="w-8 h-px"
                                                    :style="'background-color:' + previewTemplate?.colors?.primary"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2 font-mono">N° INV-2025-001</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-8 mb-6 text-xs">
                                            <div>
                                                <p class="text-[10px] uppercase tracking-widest mb-2 font-semibold"
                                                    :style="'color:' + previewTemplate?.colors?.primary">Émetteur</p>
                                                <p class="font-bold text-gray-900">Votre Entreprise</p>
                                                <p class="text-gray-500 leading-relaxed">123 Rue du Commerce<br>Dakar,
                                                    Sénégal</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] uppercase tracking-widest mb-2 font-semibold"
                                                    :style="'color:' + previewTemplate?.colors?.primary">Client</p>
                                                <p class="font-bold text-gray-900">Client Exemple SARL</p>
                                                <p class="text-gray-500 leading-relaxed">456 Avenue des
                                                    Affaires<br>Abidjan, Côte d'Ivoire</p>
                                            </div>
                                        </div>
                                        <div class="flex-1 border rounded-lg overflow-hidden"
                                            :style="'border-color:' + previewTemplate?.colors?.primary + '30'">
                                            <div class="flex py-2 px-4 text-xs font-semibold"
                                                :style="'background-color:' + previewTemplate?.colors?.primary + '15; color:' +
                                                    previewTemplate?.colors?.primary">
                                                <span class="flex-1">Description</span><span
                                                    class="w-12 text-center">Qté</span><span
                                                    class="w-20 text-right">P.U.</span><span
                                                    class="w-24 text-right">Montant</span></div>
                                            <div class="text-xs divide-y"
                                                :style="'--tw-divide-color:' + previewTemplate?.colors?.primary + '15'">
                                                <div class="flex py-2.5 px-4"><span class="flex-1">Service de
                                                        consultation</span><span
                                                        class="w-12 text-center text-gray-400">10</span><span
                                                        class="w-20 text-right text-gray-400">50 000</span><span
                                                        class="w-24 text-right font-medium">500 000</span></div>
                                                <div class="flex py-2.5 px-4"><span class="flex-1">Développement
                                                        web</span><span
                                                        class="w-12 text-center text-gray-400">1</span><span
                                                        class="w-20 text-right text-gray-400">750 000</span><span
                                                        class="w-24 text-right font-medium">750 000</span></div>
                                                <div class="flex py-2.5 px-4"><span class="flex-1">Maintenance
                                                        annuelle</span><span
                                                        class="w-12 text-center text-gray-400">1</span><span
                                                        class="w-20 text-right text-gray-400">200 000</span><span
                                                        class="w-24 text-right font-medium">200 000</span></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end mt-4">
                                            <div class="w-56 text-xs">
                                                <div class="flex justify-between py-1"><span
                                                        class="text-gray-400">Sous-total</span><span>1 450 000</span></div>
                                                <div class="flex justify-between py-1"><span class="text-gray-400">TVA
                                                        18%</span><span>261 000</span></div>
                                                <div class="flex justify-between py-2 border-t-2 mt-1"
                                                    :style="'border-color:' + previewTemplate?.colors?.primary"><span
                                                        class="font-bold text-sm"
                                                        :style="'color:' + previewTemplate?.colors?.primary">Total
                                                        TTC</span><span class="font-bold text-base"
                                                        :style="'color:' + previewTemplate?.colors?.primary">1 711 000
                                                        XOF</span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="h-1.5"
                                        :style="'background: linear-gradient(90deg, transparent,' + previewTemplate?.colors
                                            ?.primary + ', transparent)'">
                                    </div>
                                </div>
                            </template>

                            {{-- ═══════════ PREMIUM PREVIEW — Dark bg, gold text ═══════════ --}}
                            <template x-if="previewTemplate?.id === 'premium'">
                                <div class="h-full flex flex-col"
                                    :style="'background: linear-gradient(135deg,' + previewTemplate?.colors?.primary + ', #000)'">
                                    <div class="h-1 bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>
                                    <div class="p-8 flex-1 flex flex-col text-gray-300">
                                        <div class="flex justify-between items-start mb-8">
                                            <div>
                                                <div
                                                    class="w-14 h-14 border-2 border-amber-500/50 rounded-lg mb-3 flex items-center justify-center">
                                                    <span class="text-amber-400 font-bold text-lg">VE</span></div>
                                                <p class="text-white font-bold text-sm">Votre Entreprise</p>
                                                <p class="text-xs text-gray-500">123 Rue du Commerce, Dakar</p>
                                            </div>
                                            <div class="text-right">
                                                <h2
                                                    class="text-3xl font-bold bg-gradient-to-r from-amber-300 to-amber-500 bg-clip-text text-transparent">
                                                    FACTURE</h2>
                                                <p class="text-xs text-gray-500 font-mono mt-1">INV-2025-001</p>
                                                <p class="text-xs text-gray-600 mt-2">20/02/2026</p>
                                            </div>
                                        </div>
                                        <div class="mb-6 p-4 rounded-lg border border-amber-500/20 bg-white/5">
                                            <p class="text-[10px] uppercase tracking-widest text-amber-500/70 mb-1">
                                                Facturer à</p>
                                            <p class="text-white font-semibold text-sm">Client Exemple SARL</p>
                                            <p class="text-xs text-gray-500">456 Avenue des Affaires, Abidjan</p>
                                        </div>
                                        <div class="flex-1 rounded-lg overflow-hidden border border-gray-700">
                                            <div class="flex py-2 px-4 text-xs font-semibold text-amber-400 bg-white/5">
                                                <span class="flex-1">Description</span><span
                                                    class="w-12 text-center">Qté</span><span
                                                    class="w-20 text-right">P.U.</span><span
                                                    class="w-24 text-right">Total</span></div>
                                            <div class="text-xs divide-y divide-gray-800">
                                                <div class="flex py-2.5 px-4"><span class="flex-1 text-gray-300">Service
                                                        de consultation</span><span
                                                        class="w-12 text-center text-gray-500">10</span><span
                                                        class="w-20 text-right text-gray-500">50 000</span><span
                                                        class="w-24 text-right text-amber-300">500 000</span></div>
                                                <div class="flex py-2.5 px-4"><span
                                                        class="flex-1 text-gray-300">Développement web</span><span
                                                        class="w-12 text-center text-gray-500">1</span><span
                                                        class="w-20 text-right text-gray-500">750 000</span><span
                                                        class="w-24 text-right text-amber-300">750 000</span></div>
                                                <div class="flex py-2.5 px-4"><span
                                                        class="flex-1 text-gray-300">Maintenance annuelle</span><span
                                                        class="w-12 text-center text-gray-500">1</span><span
                                                        class="w-20 text-right text-gray-500">200 000</span><span
                                                        class="w-24 text-right text-amber-300">200 000</span></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end mt-4">
                                            <div class="w-56 text-xs">
                                                <div class="flex justify-between py-1"><span
                                                        class="text-gray-500">Sous-total</span><span
                                                        class="text-gray-300">1 450 000</span></div>
                                                <div class="flex justify-between py-1"><span class="text-gray-500">TVA
                                                        18%</span><span class="text-gray-300">261 000</span></div>
                                                <div class="flex justify-between py-2 border-t border-amber-500/40 mt-1">
                                                    <span class="font-bold text-amber-400">Total</span><span
                                                        class="font-bold text-lg text-amber-400">1 711 000 XOF</span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="h-1 bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>
                                </div>
                            </template>

                            {{-- ═══════════ AFRICAN PREVIEW — Colorful borders, patterns ═══════════ --}}
                            <template x-if="previewTemplate?.id === 'african'">
                                <div class="h-full bg-amber-50/50 flex flex-col">
                                    <div
                                        class="h-4 bg-gradient-to-r from-red-600 via-yellow-500 to-green-600 flex items-center justify-center gap-1">
                                        <span class="text-white text-[8px] font-bold tracking-[0.3em]">◆ ◆ ◆ ◆ ◆ ◆ ◆ ◆ ◆ ◆
                                            ◆ ◆</span>
                                    </div>
                                    <div class="flex flex-1">
                                        <div class="w-2 bg-gradient-to-b from-red-600 via-yellow-500 to-green-600"></div>
                                        <div class="flex-1 p-6 flex flex-col">
                                            <div class="flex justify-between items-start mb-6">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-14 h-14 bg-gradient-to-br from-red-500 via-yellow-400 to-green-500 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-sm">VE</span></div>
                                                    <div>
                                                        <p class="font-bold text-gray-900 text-sm">Votre Entreprise</p>
                                                        <p class="text-xs text-gray-500">Dakar, Sénégal</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <h2 class="text-2xl font-bold text-green-800">FACTURE</h2>
                                                    <p class="text-xs text-gray-500 font-mono">INV-2025-001</p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3 mb-4">
                                                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                                    <p class="text-[10px] uppercase text-red-400 mb-1 font-semibold">Client
                                                    </p>
                                                    <p class="text-xs font-bold">Client Exemple SARL</p>
                                                    <p class="text-[11px] text-gray-500">Abidjan, Côte d'Ivoire</p>
                                                </div>
                                                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                                    <p class="text-[10px] uppercase text-green-400 mb-1 font-semibold">
                                                        Dates</p>
                                                    <p class="text-xs">Émission: 20/02/2026</p>
                                                    <p class="text-xs">Échéance: 22/03/2026</p>
                                                </div>
                                            </div>
                                            <div class="flex-1 rounded-lg overflow-hidden border border-yellow-300">
                                                <div
                                                    class="flex py-2 px-3 text-xs font-bold text-white bg-gradient-to-r from-red-600 to-green-600">
                                                    <span class="flex-1">Description</span><span
                                                        class="w-12 text-center">Qté</span><span
                                                        class="w-20 text-right">Prix</span><span
                                                        class="w-24 text-right">Total</span></div>
                                                <div class="text-xs divide-y divide-yellow-100 bg-white">
                                                    <div class="flex py-2.5 px-3 bg-yellow-50/50"><span
                                                            class="flex-1">Consultation</span><span
                                                            class="w-12 text-center text-gray-500">10</span><span
                                                            class="w-20 text-right text-gray-500">50 000</span><span
                                                            class="w-24 text-right font-semibold">500 000</span></div>
                                                    <div class="flex py-2.5 px-3"><span class="flex-1">Développement
                                                            web</span><span
                                                            class="w-12 text-center text-gray-500">1</span><span
                                                            class="w-20 text-right text-gray-500">750 000</span><span
                                                            class="w-24 text-right font-semibold">750 000</span></div>
                                                    <div class="flex py-2.5 px-3 bg-yellow-50/50"><span
                                                            class="flex-1">Maintenance</span><span
                                                            class="w-12 text-center text-gray-500">1</span><span
                                                            class="w-20 text-right text-gray-500">200 000</span><span
                                                            class="w-24 text-right font-semibold">200 000</span></div>
                                                </div>
                                            </div>
                                            <div class="flex justify-end mt-4">
                                                <div class="w-56 text-xs">
                                                    <div class="flex justify-between py-1"><span
                                                            class="text-gray-400">Sous-total</span><span>1 450 000</span>
                                                    </div>
                                                    <div class="flex justify-between py-1"><span class="text-gray-400">TVA
                                                            18%</span><span>261 000</span></div>
                                                    <div
                                                        class="flex justify-between py-2 px-3 rounded-lg mt-1 text-white text-sm font-bold bg-gradient-to-r from-red-600 via-yellow-500 to-green-600">
                                                        <span>Total</span><span>1 711 000 XOF</span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-2 bg-gradient-to-b from-green-600 via-yellow-500 to-red-600"></div>
                                    </div>
                                    <div
                                        class="h-4 bg-gradient-to-r from-green-600 via-yellow-500 to-red-600 flex items-center justify-center gap-1">
                                        <span class="text-white text-[8px] font-bold tracking-[0.3em]">◆ ◆ ◆ ◆ ◆ ◆ ◆ ◆ ◆ ◆
                                            ◆ ◆</span>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button @click="showPreview = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upgrade Modal -->
        <div x-show="showUpgradeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/75" @click="showUpgradeModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <button @click="showUpgradeModal = false"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Template Premium</h3>
                        <p class="text-gray-600 mb-6">
                            Ce template nécessite le plan <span class="font-semibold" x-text="requiredPlan"></span> ou
                            supérieur.
                        </p>
                        <div class="space-y-3">
                            <a href="{{ route('client.settings.index') }}"
                                class="block w-full py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl font-semibold hover:from-purple-600 hover:to-pink-600">
                                Mettre à niveau maintenant
                            </a>
                            <button @click="showUpgradeModal = false"
                                class="block w-full py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200">
                                Plus tard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
