<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            return redirect('/admin/login');
        }

        // Seuls les admins actifs avec un tenant peuvent accéder à /admin
        $user = auth()->user();

        if ($user->role !== 'admin') {
            abort(403, 'Accès refusé. Cette interface est réservée aux administrateurs.');
        }

        if (!$user->is_active || !$user->tenant_id) {
            abort(403, 'Accès refusé. Votre compte est inactif ou non configuré.');
        }

        return $next($request);
    }
}
