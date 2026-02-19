@extends('layouts.app')

@section('title', 'Invitation à rejoindre une équipe')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center space-x-2">
                    <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-2xl font-bold text-gray-900">Invoice SaaS</span>
                </a>
            </div>

            <!-- Contenu de l'invitation -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Vous êtes invité(e) !</h1>
                <p class="text-gray-600">
                    <strong>{{ $invitation->inviter->name ?? 'Un membre' }}</strong> vous invite à rejoindre l'équipe 
                    <strong>{{ $invitation->tenant->company_name ?? $invitation->tenant->name }}</strong>
                </p>
            </div>

            <!-- Détails de l'invitation -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Rôle attribué</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        @switch($invitation->role)
                            @case('admin') Administrateur @break
                            @case('member') Membre @break
                            @case('viewer') Lecteur @break
                            @default {{ ucfirst($invitation->role) }}
                        @endswitch
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Expire le</span>
                    <span class="text-gray-900">{{ $invitation->expires_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            @if(auth()->check())
                <!-- Utilisateur connecté -->
                <form action="{{ route('invitation.accept', $invitation->token) }}" method="POST" class="space-y-4">
                    @csrf
                    <p class="text-sm text-gray-600 text-center">
                        Vous êtes connecté(e) en tant que <strong>{{ auth()->user()->email }}</strong>
                    </p>
                    <button type="submit" 
                            class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Accepter l'invitation
                    </button>
                </form>
                <form action="{{ route('invitation.decline', $invitation->token) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" 
                            class="w-full py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        Décliner
                    </button>
                </form>
            @else
                <!-- Utilisateur non connecté -->
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 text-center">
                        Connectez-vous ou créez un compte pour accepter cette invitation.
                    </p>
                    
                    <a href="{{ route('login', ['redirect' => route('invitation.show', $invitation->token)]) }}" 
                       class="block w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-center">
                        Se connecter
                    </a>
                    
                    <a href="{{ route('register', ['invitation' => $invitation->token, 'email' => $invitation->email]) }}" 
                       class="block w-full py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition text-center">
                        Créer un compte
                    </a>
                </div>
            @endif

            <!-- Message d'erreur -->
            @if($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif
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
