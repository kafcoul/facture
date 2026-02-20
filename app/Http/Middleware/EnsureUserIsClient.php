<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsClient
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
            return redirect('/login');
        }

        $user = auth()->user();

        // Les admins doivent aller sur /admin
        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        // Seuls les clients actifs avec un tenant peuvent accéder
        if ($user->role !== 'client') {
            abort(403, 'Accès refusé. Cette interface est réservée aux clients.');
        }

        if (!$user->is_active || !$user->tenant_id) {
            abort(403, 'Accès refusé. Votre compte est inactif ou non configuré.');
        }

        return $next($request);
    }
}
