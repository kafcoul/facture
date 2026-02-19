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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Votre plan : {{ $planNames[$userPlan] }}</p>
                    <p class="text-sm text-gray-600">
                        @if($userPlan === 'starter')
                            2 templates disponibles - Passez au Pro pour plus de choix
                        @elseif($userPlan === 'pro')
                            5 templates disponibles - Passez à Enterprise pour tous les templates
                        @else
                            Accès à tous les templates premium
                        @endif
                    </p>
                </div>
            </div>
            @if($userPlan !== 'enterprise')
            <a href="{{ route('client.settings.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                Mettre à niveau →
            </a>
            @endif
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($templates as $template)
        @php
            $planHierarchy = ['starter' => 1, 'pro' => 2, 'enterprise' => 3];
            $userPlanLevel = $planHierarchy[$userPlan] ?? 1;
            $templatePlanLevel = $planHierarchy[$template['plan']] ?? 1;
            $canUse = $userPlanLevel >= $templatePlanLevel;
            $isActive = $currentTemplate === $template['id'];
        @endphp
        
        <div class="relative group">
            <div class="bg-white rounded-xl shadow-sm border-2 overflow-hidden transition-all duration-300 
                {{ $isActive ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300 hover:shadow-md' }}
                {{ !$canUse ? 'opacity-75' : '' }}">
                
                <!-- Template Preview -->
                <div class="aspect-[3/4] bg-gradient-to-br from-gray-50 to-gray-100 relative overflow-hidden">
                    <!-- Mini Invoice Preview -->
                    <div class="absolute inset-4 bg-white rounded-lg shadow-lg p-3 transform transition-transform group-hover:scale-105">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-8 h-8 rounded" style="background-color: {{ $template['colors']['primary'] }}"></div>
                            <div class="text-right">
                                <div class="h-2 w-12 bg-gray-300 rounded mb-1"></div>
                                <div class="h-1.5 w-8 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                        
                        <!-- Content Lines -->
                        <div class="space-y-2 mb-3">
                            <div class="h-1.5 w-full bg-gray-200 rounded"></div>
                            <div class="h-1.5 w-3/4 bg-gray-200 rounded"></div>
                            <div class="h-1.5 w-5/6 bg-gray-200 rounded"></div>
                        </div>
                        
                        <!-- Table -->
                        <div class="border-t border-b border-gray-200 py-2 mb-2">
                            <div class="flex justify-between mb-1">
                                <div class="h-1.5 w-1/3 bg-gray-300 rounded"></div>
                                <div class="h-1.5 w-1/6 bg-gray-300 rounded"></div>
                            </div>
                            <div class="flex justify-between mb-1">
                                <div class="h-1 w-1/2 bg-gray-200 rounded"></div>
                                <div class="h-1 w-1/6 bg-gray-200 rounded"></div>
                            </div>
                            <div class="flex justify-between">
                                <div class="h-1 w-2/5 bg-gray-200 rounded"></div>
                                <div class="h-1 w-1/6 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div class="flex justify-end">
                            <div class="text-right">
                                <div class="h-2 w-16 rounded mb-1" style="background-color: {{ $template['colors']['primary'] }}"></div>
                                <div class="h-1 w-10 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Lock Overlay for Locked Templates -->
                    @if(!$canUse)
                    <div class="absolute inset-0 bg-gray-900/40 flex items-center justify-center">
                        <div class="bg-white rounded-full p-3 shadow-lg">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                    @endif

                    <!-- Active Badge -->
                    @if($isActive)
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500 text-white shadow">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Actif
                        </span>
                    </div>
                    @endif

                    <!-- Plan Badge -->
                    @if($template['plan'] !== 'starter')
                    <div class="absolute top-2 left-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium shadow
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
                            <div class="w-4 h-4 rounded-full border-2 border-white shadow" style="background-color: {{ $template['colors']['primary'] }}"></div>
                            <div class="w-4 h-4 rounded-full border-2 border-white shadow" style="background-color: {{ $template['colors']['secondary'] }}"></div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2 mt-3">
                        @if($canUse)
                            @if($isActive)
                            <span class="flex-1 text-center py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium">
                                Template actuel
                            </span>
                            @else
                            <form action="{{ route('client.templates.select', $template['id']) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                    Utiliser
                                </button>
                            </form>
                            @endif
                            <button type="button" 
                                    @click="showPreview = true; previewTemplate = {{ json_encode($template) }}"
                                    class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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
            
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" x-transition>
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900" x-text="previewTemplate?.name"></h3>
                        <p class="text-sm text-gray-600" x-text="previewTemplate?.description"></p>
                    </div>
                    <button @click="showPreview = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <!-- Full Invoice Preview -->
                    <div class="bg-white shadow-lg rounded-lg mx-auto max-w-2xl" style="aspect-ratio: 1/1.414;">
                        <div class="p-8 h-full flex flex-col">
                            <!-- Invoice Header -->
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <div class="w-16 h-16 rounded-lg mb-3 flex items-center justify-center text-white font-bold text-xl" :style="'background-color: ' + previewTemplate?.colors?.primary">
                                        LOGO
                                    </div>
                                    <p class="font-bold text-gray-900">Votre Entreprise</p>
                                    <p class="text-sm text-gray-600">123 Rue du Commerce</p>
                                    <p class="text-sm text-gray-600">Dakar, Sénégal</p>
                                </div>
                                <div class="text-right">
                                    <h2 class="text-2xl font-bold" :style="'color: ' + previewTemplate?.colors?.primary">FACTURE</h2>
                                    <p class="text-gray-600 mt-1">N° INV-2025-001</p>
                                    <p class="text-sm text-gray-500 mt-2">Date: 30/11/2025</p>
                                    <p class="text-sm text-gray-500">Échéance: 30/12/2025</p>
                                </div>
                            </div>

                            <!-- Client Info -->
                            <div class="mb-6 p-4 rounded-lg" :style="'background-color: ' + previewTemplate?.colors?.primary + '10'">
                                <p class="text-sm font-medium text-gray-500 mb-1">Facturer à:</p>
                                <p class="font-bold text-gray-900">Client Exemple SARL</p>
                                <p class="text-sm text-gray-600">456 Avenue des Affaires</p>
                                <p class="text-sm text-gray-600">Abidjan, Côte d'Ivoire</p>
                            </div>

                            <!-- Items Table -->
                            <div class="flex-1">
                                <table class="w-full mb-6">
                                    <thead>
                                        <tr class="border-b-2" :style="'border-color: ' + previewTemplate?.colors?.primary">
                                            <th class="text-left py-2 text-sm font-semibold text-gray-700">Description</th>
                                            <th class="text-center py-2 text-sm font-semibold text-gray-700">Qté</th>
                                            <th class="text-right py-2 text-sm font-semibold text-gray-700">Prix</th>
                                            <th class="text-right py-2 text-sm font-semibold text-gray-700">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr>
                                            <td class="py-3 text-sm text-gray-900">Service de consultation</td>
                                            <td class="py-3 text-sm text-gray-600 text-center">10</td>
                                            <td class="py-3 text-sm text-gray-600 text-right">50 000 XOF</td>
                                            <td class="py-3 text-sm text-gray-900 text-right font-medium">500 000 XOF</td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-sm text-gray-900">Développement web</td>
                                            <td class="py-3 text-sm text-gray-600 text-center">1</td>
                                            <td class="py-3 text-sm text-gray-600 text-right">750 000 XOF</td>
                                            <td class="py-3 text-sm text-gray-900 text-right font-medium">750 000 XOF</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totals -->
                            <div class="border-t pt-4">
                                <div class="flex justify-end">
                                    <div class="w-64">
                                        <div class="flex justify-between py-1">
                                            <span class="text-gray-600">Sous-total</span>
                                            <span class="text-gray-900">1 250 000 XOF</span>
                                        </div>
                                        <div class="flex justify-between py-1">
                                            <span class="text-gray-600">TVA (18%)</span>
                                            <span class="text-gray-900">225 000 XOF</span>
                                        </div>
                                        <div class="flex justify-between py-2 border-t-2 mt-2" :style="'border-color: ' + previewTemplate?.colors?.primary">
                                            <span class="font-bold text-gray-900">Total</span>
                                            <span class="font-bold text-lg" :style="'color: ' + previewTemplate?.colors?.primary">1 475 000 XOF</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button @click="showPreview = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
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
                <button @click="showUpgradeModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Template Premium</h3>
                    <p class="text-gray-600 mb-6">
                        Ce template nécessite le plan <span class="font-semibold" x-text="requiredPlan"></span> ou supérieur.
                    </p>
                    <div class="space-y-3">
                        <a href="{{ route('client.settings.index') }}" class="block w-full py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl font-semibold hover:from-purple-600 hover:to-pink-600">
                            Mettre à niveau maintenant
                        </a>
                        <button @click="showUpgradeModal = false" class="block w-full py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200">
                            Plus tard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
