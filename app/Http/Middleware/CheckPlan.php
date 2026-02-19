<?php

namespace App\Http\Middleware;

use App\Services\PlanService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$plans
     */
    public function handle(Request $request, Closure $next, ...$plans): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $userPlan = $user->plan ?? 'starter';
        
        // Vérifier si le plan de l'utilisateur est dans les plans autorisés
        if (!in_array($userPlan, $plans)) {
            // Vérifier aussi si l'utilisateur est en trial avec un plan supérieur
            $tenant = $user->tenant;
            $tenantPlan = $tenant->plan ?? $userPlan;
            
            if (!in_array($tenantPlan, $plans)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Cette fonctionnalité nécessite un plan supérieur.',
                        'required_plans' => $plans,
                        'current_plan' => $userPlan,
                        'upgrade_url' => route('client.billing'),
                    ], 403);
                }
                
                $planNames = array_map(fn($p) => PlanService::getPlan($p)['name'] ?? ucfirst($p), $plans);
                
                return redirect()
                    ->route('client.billing')
                    ->with('error', 'Cette fonctionnalité nécessite le plan ' . implode(' ou ', $planNames) . '. Mettez à niveau votre abonnement.');
            }
        }

        return $next($request);
    }
}
