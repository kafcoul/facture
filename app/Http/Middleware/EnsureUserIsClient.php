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
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Tous les utilisateurs authentifiés peuvent accéder au dashboard client
        // Sauf les super_admin qui doivent aller sur /admin
        // Les rôles autorisés: client, user, admin (ce sont tous des clients de la plateforme)
        $allowedRoles = ['client', 'user', 'admin'];
        
        if (!in_array($user->role, $allowedRoles)) {
            // Si c'est un super_admin ou propriétaire, le rediriger vers /admin
            if ($user->role === 'super_admin' || $user->is_owner) {
                return redirect('/admin');
            }
            abort(403, 'Accès refusé. Cette interface est réservée aux clients.');
        }

        return $next($request);
    }
}
