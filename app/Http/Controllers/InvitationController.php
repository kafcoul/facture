<?php

namespace App\Http\Controllers;

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvitationController extends Controller
{
    /**
     * Affiche la page d'acceptation d'invitation
     */
    public function show($token)
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        // Vérifier si l'invitation est valide
        if (!$invitation->isValid()) {
            return view('invitations.invalid', [
                'reason' => $invitation->isExpired() ? 'expired' : 'invalid',
            ]);
        }

        // Vérifier si l'utilisateur est connecté
        $user = Auth::user();
        $existingUser = User::where('email', $invitation->email)->first();

        return view('invitations.accept', [
            'invitation' => $invitation,
            'user' => $user,
            'existingUser' => $existingUser,
            'requiresRegistration' => !$existingUser && !$user,
            'requiresLogin' => $existingUser && !$user,
        ]);
    }

    /**
     * Accepte l'invitation
     */
    public function accept(Request $request, $token)
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        // Vérifier si l'invitation est valide
        if (!$invitation->isValid()) {
            return redirect()->route('home')
                ->with('error', 'Cette invitation n\'est plus valide.');
        }

        $user = Auth::user();

        // Si l'utilisateur n'est pas connecté
        if (!$user) {
            // Vérifier si un compte existe
            $existingUser = User::where('email', $invitation->email)->first();

            if ($existingUser) {
                // L'utilisateur doit se connecter
                return redirect()->route('login')
                    ->with('info', 'Veuillez vous connecter pour accepter l\'invitation.')
                    ->with('redirect_after_login', route('invitation.accept', $token));
            }

            // Créer un nouveau compte
            $request->validate([
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => 'Le nom est requis.',
                'password.required' => 'Le mot de passe est requis.',
                'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'is_active' => true,
            ]);

            Auth::login($user);
        }

        // Accepter l'invitation
        try {
            $member = $invitation->accept($user);
            
            return redirect()->route('client.dashboard')
                ->with('success', "Bienvenue dans l'équipe {$invitation->tenant->name} !");
        } catch (\Exception $e) {
            return redirect()->route('home')
                ->with('error', 'Une erreur est survenue lors de l\'acceptation de l\'invitation.');
        }
    }

    /**
     * Refuse l'invitation
     */
    public function decline($token)
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isPending()) {
            $invitation->decline();
        }

        return redirect()->route('home')
            ->with('info', 'L\'invitation a été refusée.');
    }
}
