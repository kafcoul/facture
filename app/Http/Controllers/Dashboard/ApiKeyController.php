<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    /**
     * Affiche la page de gestion des clés API
     */
    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        // Vérifier le plan Enterprise
        $plan = $user->plan ?? 'starter';
        if ($plan !== 'enterprise') {
            return redirect()->route('client.dashboard')
                ->with('error', 'L\'accès API est réservé au plan Enterprise.');
        }

        // Récupérer les clés API
        $apiKeys = ApiKey::where('tenant_id', $tenant->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.api-keys.index', [
            'apiKeys' => $apiKeys,
        ]);
    }

    /**
     * Génère une nouvelle clé API
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        // Vérifier le plan
        if (($user->plan ?? 'starter') !== 'enterprise') {
            return back()->with('error', 'Fonctionnalité réservée au plan Enterprise.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'string',
            'expires_in' => 'nullable|integer|min:1|max:365',
        ], [
            'name.required' => 'Le nom de la clé est requis.',
        ]);

        // Limiter le nombre de clés par tenant (max 10)
        $keyCount = ApiKey::where('tenant_id', $tenant->id)->count();
        if ($keyCount >= 10) {
            return back()->with('error', 'Vous avez atteint la limite de 10 clés API.');
        }

        // Générer la clé
        $result = ApiKey::generate([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'name' => $request->name,
            'permissions' => $request->permissions ?? ['*'],
            'rate_limit_per_minute' => 60,
            'expires_at' => $request->expires_in 
                ? now()->addDays($request->expires_in) 
                : null,
        ]);

        // Stocker temporairement la clé complète en session pour l'afficher une seule fois
        session()->flash('new_api_key', $result['full_key']);

        return back()->with('success', 'Clé API créée avec succès. Copiez-la maintenant, elle ne sera plus visible.');
    }

    /**
     * Met à jour une clé API
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $apiKey = ApiKey::where('tenant_id', $user->tenant_id)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ]);

        $apiKey->update([
            'name' => $request->name,
            'permissions' => $request->permissions ?? $apiKey->permissions,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Clé API mise à jour.');
    }

    /**
     * Révoque une clé API
     */
    public function revoke($id)
    {
        $user = auth()->user();
        $apiKey = ApiKey::where('tenant_id', $user->tenant_id)
            ->where('id', $id)
            ->firstOrFail();

        $apiKey->revoke();

        return back()->with('success', 'Clé API révoquée.');
    }

    /**
     * Supprime une clé API
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $apiKey = ApiKey::where('tenant_id', $user->tenant_id)
            ->where('id', $id)
            ->firstOrFail();

        $apiKey->delete();

        return back()->with('success', 'Clé API supprimée.');
    }

    /**
     * Affiche la documentation API
     */
    public function documentation()
    {
        $user = auth()->user();

        // Vérifier le plan
        if (($user->plan ?? 'starter') !== 'enterprise') {
            return redirect()->route('client.dashboard')
                ->with('error', 'L\'accès API est réservé au plan Enterprise.');
        }

        return view('dashboard.api-keys.documentation');
    }
}
