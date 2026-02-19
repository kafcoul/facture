<?php

namespace App\Http\Middleware;

use App\Services\PlanService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: Vérifier si l'essai gratuit est expiré
 * 
 * Si le trial est expiré ET que l'utilisateur n'a pas de plan payant actif,
 * il est redirigé vers la page de mise à niveau.
 */
class CheckTrialExpired
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Les super_admins ne sont pas concernés
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Si pas de trial_ends_at, le trial n'est pas configuré → passer
        if (!$user->trial_ends_at) {
            return $next($request);
        }

        // Plan Starter gratuit: pas d'expiration de trial
        $plan = $user->plan ?? 'starter';
        if ($plan === 'starter') {
            return $next($request);
        }

        // Si trial expiré pour un plan payant
        if (PlanService::isTrialExpired($user)) {
            // Vérifier aussi le tenant
            $tenant = $user->tenant;
            $tenantTrialExpired = $tenant ? PlanService::isTrialExpired($tenant) : true;

            if ($tenantTrialExpired) {
                // Permettre l'accès à la page de billing et déconnexion
                $allowedRoutes = [
                    'client.billing',
                    'client.billing.upgrade',
                    'client.billing.downgrade',
                    'client.profile.edit',
                    'logout',
                ];

                $currentRoute = $request->route()?->getName();
                if ($currentRoute && in_array($currentRoute, $allowedRoutes)) {
                    return $next($request);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Votre essai gratuit est terminé. Veuillez mettre à niveau votre abonnement.',
                        'trial_expired' => true,
                        'upgrade_url' => route('client.billing'),
                    ], 402);
                }

                return redirect()
                    ->route('client.billing')
                    ->with('warning', '⏰ Votre période d\'essai de 30 jours est terminée. Choisissez un plan pour continuer à utiliser InvoiceSaaS.');
            }
        }

        return $next($request);
    }
}
