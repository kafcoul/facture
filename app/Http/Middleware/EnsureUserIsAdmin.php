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

        // RESTRICTION: Seul le propriétaire peut accéder à /admin
        // Liste des emails autorisés (ajoutez votre email ici)
        $authorizedEmails = [
            env('SUPER_ADMIN_EMAIL', 'leaudouce0@gmail.com'),
        ];

        // Vérifier si l'email de l'utilisateur est autorisé
        if (!in_array(auth()->user()->email, $authorizedEmails)) {
            abort(403, 'Accès refusé. Seul le propriétaire de la plateforme peut accéder à cette interface.');
        }

        // Vérifier également le rôle admin pour double sécurité
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé. Cette interface est réservée au propriétaire.');
        }

        return $next($request);
    }
}
