<?php

namespace App\Http\Middleware\Tenant;

use App\Domain\Tenant\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour identifier le tenant par sous-domaine
 */
class IdentifyTenantByDomain
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
        $host = $request->getHost();
        
        // Extraire le sous-domaine (ex: tenant1.monapp.com -> tenant1)
        $subdomain = explode('.', $host)[0];

        // Ignorer pour localhost et www
        if (in_array($subdomain, ['localhost', 'www', '127', 'app'])) {
            return $next($request);
        }

        // Chercher le tenant par sous-domaine
        $tenant = Tenant::where('domain', $subdomain)
                        ->orWhere('slug', $subdomain)
                        ->active()
                        ->first();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Stocker le tenant dans la requête
        $request->attributes->set('tenant', $tenant);
        session(['tenant_id' => $tenant->id]);

        return $next($request);
    }
}
