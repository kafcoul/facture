<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware pour résoudre et vérifier le tenant de l'utilisateur connecté
 */
class ResolveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Vérifier que l'utilisateur a un tenant
        if (!$user->tenant_id) {
            abort(403, 'No tenant associated with your account. Please contact support.');
        }

        // Vérifier que le tenant est actif
        if ($user->tenant && !$user->tenant->isActive()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your subscription has expired. Please contact support.');
        }

        // Stocker le tenant dans la session pour y accéder facilement
        session(['tenant_id' => $user->tenant_id]);

        return $next($request);
    }
}
