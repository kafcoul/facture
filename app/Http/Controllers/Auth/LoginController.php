<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\TwoFactorAuthenticatable;

class LoginController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traite la connexion
     *
     * Vérifie les identifiants puis redirige vers le challenge 2FA
     * si l'utilisateur a activé l'authentification à deux facteurs,
     * sinon connecte directement.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'password.required' => 'Le mot de passe est requis.',
        ]);

        $remember = $request->boolean('remember');

        // Récupérer l'utilisateur par email
        $user = User::where('email', $credentials['email'])->first();

        // Vérifier les identifiants sans connecter
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
            ]);
        }

        // ── 2FA Challenge ─────────────────────────────────────────────
        // Si l'utilisateur a activé et confirmé le 2FA, on ne le connecte
        // pas encore. On stocke son ID en session et on redirige vers la
        // page de challenge 2FA (gérée par Fortify).
        if ($this->hasTwoFactorEnabled($user)) {
            $request->session()->put([
                'login.id'       => $user->getKey(),
                'login.remember' => $remember,
            ]);

            return redirect()->route('two-factor.login');
        }

        // ── Connexion directe (pas de 2FA) ────────────────────────────
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Mettre à jour la date de dernière connexion
        $user->update(['last_login_at' => now()]);

        // Rediriger selon le rôle
        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/client');
    }

    /**
     * Vérifie si l'utilisateur a le 2FA activé et confirmé.
     */
    protected function hasTwoFactorEnabled(User $user): bool
    {
        return ! empty($user->two_factor_secret)
            && ! is_null($user->two_factor_confirmed_at)
            && in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user));
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
