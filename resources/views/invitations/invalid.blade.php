@extends('layouts.app')

@section('title', 'Invitation invalide')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center space-x-2">
                    <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-2xl font-bold text-gray-900">Invoice SaaS</span>
                </a>
            </div>

            <!-- Icône d'erreur -->
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <!-- Message -->
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Invitation invalide</h1>
            <p class="text-gray-600 mb-6">
                @if(isset($reason))
                    @switch($reason)
                        @case('expired')
                            Cette invitation a expiré. Veuillez demander une nouvelle invitation à l'administrateur de l'équipe.
                            @break
                        @case('already_used')
                            Cette invitation a déjà été utilisée.
                            @break
                        @case('declined')
                            Cette invitation a été déclinée.
                            @break
                        @case('already_member')
                            Vous êtes déjà membre de cette équipe.
                            @break
                        @default
                            Cette invitation n'est plus valide ou n'existe pas.
                    @endswitch
                @else
                    Cette invitation n'est plus valide ou n'existe pas.
                @endif
            </p>

            <!-- Actions -->
            <div class="space-y-3">
                @auth
                <a href="{{ route('client.dashboard') }}" 
                   class="block w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Aller au tableau de bord
                </a>
                @else
                <a href="{{ route('login') }}" 
                   class="block w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Se connecter
                </a>
                <a href="{{ route('register') }}" 
                   class="block w-full py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                    Créer un compte
                </a>
                @endauth
            </div>
        </div>

        <!-- Lien retour -->
        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900">
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
