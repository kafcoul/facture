<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Str;

class RegisterWithPlanController extends Controller
{
    /**
     * Afficher le formulaire d'inscription avec choix de plan
     */
    public function showRegistrationForm()
    {
        return view('auth.register-with-plan');
    }

    /**
     * Traiter l'inscription avec le plan choisi
     */
    public function register(Request $request)
    {
        // Validation avec messages en français
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'plan' => ['required', 'in:starter,pro,enterprise'],
            'terms' => ['accepted'],
        ], [
            'company_name.required' => 'Le nom de l\'entreprise est obligatoire.',
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'plan.required' => 'Veuillez choisir un plan.',
            'terms.accepted' => 'Vous devez accepter les conditions générales.',
        ]);

        // 1. Créer le TENANT (l'entreprise)
        $tenant = Tenant::create([
            'name' => $validated['company_name'],
            'slug' => Str::slug($validated['company_name']) . '-' . Str::random(6),
            'plan' => $validated['plan'],
            'trial_ends_at' => now()->addDays(30), // 30 jours d'essai
            'is_active' => true,
        ]);

        // 2. Créer l'UTILISATEUR (admin du tenant)
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin', // Premier utilisateur = admin
            'is_active' => true,
            'plan' => $validated['plan'],
            'trial_ends_at' => now()->addDays(30), // 30 jours d'essai
        ]);

        // 3. Déclencher l'événement Registered (envoi email de vérification)
        event(new Registered($user));

        // 3b. Envoyer la notification de bienvenue
        $user->notify(new WelcomeNotification($validated['plan']));

        // 4. Connecter automatiquement l'utilisateur
        Auth::login($user);

        // 5. Rediriger vers le dashboard CLIENT (interface principale de facturation)
        // Même si l'utilisateur est admin, il commence par l'interface client
        return redirect()->route('client.index')
            ->with('success', "🎉 Bienvenue {$user->name} ! Votre compte {$tenant->plan} est prêt. Essai gratuit de 30 jours activé !");
    }
}
