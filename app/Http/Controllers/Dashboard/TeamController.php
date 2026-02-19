<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamInvitationMail;

class TeamController extends Controller
{
    /**
     * Affiche la page de gestion d'équipe
     */
    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        // Vérifier le plan Enterprise
        $plan = $user->plan ?? 'starter';
        if ($plan !== 'enterprise') {
            return redirect()->route('client.dashboard')
                ->with('error', 'La gestion d\'équipe est réservée au plan Enterprise.');
        }

        // Récupérer les membres de l'équipe
        $members = TeamMember::where('tenant_id', $tenant->id)
            ->with('user')
            ->orderBy('role')
            ->orderBy('created_at')
            ->get();

        // Récupérer les invitations en attente
        $pendingInvitations = TeamInvitation::where('tenant_id', $tenant->id)
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        // Vérifier si l'utilisateur est admin
        $currentMember = TeamMember::where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->first();

        $canManageTeam = $currentMember && $currentMember->isAdmin();

        return view('dashboard.team.index', [
            'members' => $members,
            'pendingInvitations' => $pendingInvitations,
            'canManageTeam' => $canManageTeam,
            'roles' => TeamMember::ROLES,
        ]);
    }

    /**
     * Envoie une invitation
     */
    public function invite(Request $request)
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        // Vérifier le plan
        if (($user->plan ?? 'starter') !== 'enterprise') {
            return back()->with('error', 'Fonctionnalité réservée au plan Enterprise.');
        }

        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'role' => 'required|in:admin,member,viewer',
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'role.required' => 'Le rôle est requis.',
            'role.in' => 'Le rôle sélectionné est invalide.',
        ]);

        // Vérifier si l'email n'est pas déjà membre
        $existingUser = User::where('email', $request->email)
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($existingUser) {
            return back()->with('error', 'Cet utilisateur est déjà membre de votre équipe.');
        }

        // Vérifier s'il n'y a pas déjà une invitation en attente
        $existingInvitation = TeamInvitation::where('tenant_id', $tenant->id)
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return back()->with('error', 'Une invitation est déjà en attente pour cette adresse.');
        }

        // Créer l'invitation
        $invitation = TeamInvitation::create([
            'tenant_id' => $tenant->id,
            'invited_by' => $user->id,
            'email' => $request->email,
            'name' => $request->name,
            'role' => $request->role,
        ]);

        // Envoyer l'email d'invitation
        try {
            Mail::to($request->email)->send(new TeamInvitationMail($invitation));
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas bloquer
            Log::error('Erreur envoi email invitation: ' . $e->getMessage());
        }

        return back()->with('success', "Invitation envoyée à {$request->email}");
    }

    /**
     * Annule une invitation
     */
    public function cancelInvitation($id)
    {
        $user = auth()->user();
        $invitation = TeamInvitation::where('tenant_id', $user->tenant_id)
            ->where('id', $id)
            ->firstOrFail();

        $invitation->delete();

        return back()->with('success', 'Invitation annulée.');
    }

    /**
     * Renvoie une invitation
     */
    public function resendInvitation($id)
    {
        $user = auth()->user();
        $invitation = TeamInvitation::where('tenant_id', $user->tenant_id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Mettre à jour la date d'expiration
        $invitation->update(['expires_at' => now()->addDays(7)]);

        // Renvoyer l'email
        try {
            Mail::to($invitation->email)->send(new TeamInvitationMail($invitation));
        } catch (\Exception $e) {
            Log::error('Erreur renvoi email invitation: ' . $e->getMessage());
        }

        return back()->with('success', "Invitation renvoyée à {$invitation->email}");
    }

    /**
     * Met à jour le rôle d'un membre
     */
    public function updateRole(Request $request, $id)
    {
        $user = auth()->user();
        $member = TeamMember::where('tenant_id', $user->tenant_id)
            ->where('id', $id)
            ->firstOrFail();

        // Ne pas pouvoir modifier le propriétaire
        if ($member->role === 'owner') {
            return back()->with('error', 'Le rôle du propriétaire ne peut pas être modifié.');
        }

        $request->validate([
            'role' => 'required|in:admin,member,viewer',
        ]);

        $member->update(['role' => $request->role]);

        return back()->with('success', 'Rôle mis à jour.');
    }

    /**
     * Supprime un membre
     */
    public function removeMember($id)
    {
        $user = auth()->user();
        $member = TeamMember::where('tenant_id', $user->tenant_id)
            ->where('id', $id)
            ->firstOrFail();

        // Ne pas pouvoir supprimer le propriétaire
        if ($member->role === 'owner') {
            return back()->with('error', 'Le propriétaire ne peut pas être retiré.');
        }

        // Ne pas pouvoir se supprimer soi-même
        if ($member->user_id === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas vous retirer vous-même.');
        }

        // Retirer le tenant de l'utilisateur
        User::where('id', $member->user_id)->update(['tenant_id' => null]);

        $member->delete();

        return back()->with('success', 'Membre retiré de l\'équipe.');
    }
}
