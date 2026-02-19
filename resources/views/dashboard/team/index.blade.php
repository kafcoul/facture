@extends('layouts.dashboard')

@section('title', 'Gestion de l\'équipe')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion de l'équipe</h1>
            <p class="text-gray-600 mt-1">Invitez des collaborateurs et gérez les accès</p>
        </div>
        @if($canManageTeam)
        <button onclick="openInviteModal()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Inviter un membre
        </button>
        @endif
    </div>

    <!-- Messages flash -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    <!-- Invitations en attente -->
    @if($pendingInvitations->isNotEmpty())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
            </svg>
            Invitations en attente ({{ $pendingInvitations->count() }})
        </h3>
        <div class="space-y-3">
            @foreach($pendingInvitations as $invitation)
            <div class="flex items-center justify-between bg-white rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $invitation->email }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $invitation->role_label }} • Expire le {{ $invitation->expires_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                @if($canManageTeam)
                <div class="flex space-x-2">
                    <form action="{{ route('client.team.resend', $invitation->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Renvoyer
                        </button>
                    </form>
                    <form action="{{ route('client.team.cancel', $invitation->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Annuler
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Membres de l'équipe -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Membres de l'équipe ({{ $members->count() }})</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($members as $member)
            <div class="p-6 flex items-center justify-between hover:bg-gray-50">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($member->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $member->user->name ?? 'Utilisateur' }}</p>
                        <p class="text-sm text-gray-500">{{ $member->user->email ?? '' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Badge rôle -->
                    @php
                        $roleColors = [
                            'owner' => 'bg-purple-100 text-purple-800',
                            'admin' => 'bg-blue-100 text-blue-800',
                            'member' => 'bg-green-100 text-green-800',
                            'viewer' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $roleColors[$member->role] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $member->role_label }}
                    </span>

                    <!-- Date d'arrivée -->
                    <span class="text-sm text-gray-500">
                        Depuis {{ $member->joined_at ? $member->joined_at->format('d/m/Y') : $member->created_at->format('d/m/Y') }}
                    </span>

                    <!-- Actions -->
                    @if($canManageTeam && $member->role !== 'owner' && $member->user_id !== auth()->id())
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                            <div class="py-1">
                                <button onclick="openRoleModal({{ $member->id }}, '{{ $member->role }}')" 
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Modifier le rôle
                                </button>
                                <form action="{{ route('client.team.remove', $member->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir retirer ce membre ?')"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Retirer de l'équipe
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-gray-500">Aucun membre dans l'équipe</p>
                @if($canManageTeam)
                <button onclick="openInviteModal()" class="mt-4 text-blue-600 hover:text-blue-800 font-medium">
                    Inviter le premier membre
                </button>
                @endif
            </div>
            @endforelse
        </div>
    </div>

    <!-- Rôles et permissions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rôles et permissions</h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($roles as $roleKey => $roleData)
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-1">{{ $roleData['label'] }}</h4>
                <p class="text-sm text-gray-500">{{ $roleData['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Invitation -->
<div id="inviteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" x-data="{ open: false }">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeInviteModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Inviter un membre</h3>
            <form action="{{ route('client.team.invite') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="collaborateur@entreprise.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom (optionnel)</label>
                        <input type="text" name="name"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Jean Dupont">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rôle *</label>
                        <select name="role" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="admin">Administrateur</option>
                            <option value="member" selected>Membre</option>
                            <option value="viewer">Observateur</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeInviteModal()" 
                            class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                        Envoyer l'invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modification Rôle -->
<div id="roleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeRoleModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Modifier le rôle</h3>
            <form id="roleForm" method="POST">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau rôle</label>
                    <select name="role" id="roleSelect"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="admin">Administrateur</option>
                        <option value="member">Membre</option>
                        <option value="viewer">Observateur</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeRoleModal()" 
                            class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openInviteModal() {
    document.getElementById('inviteModal').classList.remove('hidden');
}

function closeInviteModal() {
    document.getElementById('inviteModal').classList.add('hidden');
}

function openRoleModal(memberId, currentRole) {
    document.getElementById('roleModal').classList.remove('hidden');
    document.getElementById('roleForm').action = '/client/team/' + memberId + '/role';
    document.getElementById('roleSelect').value = currentRole;
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.add('hidden');
}
</script>
@endsection
