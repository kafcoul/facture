<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests as BaseThrottleRequests;

/**
 * Middleware: Rate Limiting personnalisé par tenant
 * 
 * Limite les requêtes API par tenant pour éviter les abus
 */
class ThrottleRequests extends BaseThrottleRequests
{
    /**
     * Résoudre la clé de throttling basée sur le tenant
     */
    protected function resolveRequestSignature($request)
    {
        // Si l'utilisateur est authentifié, utiliser tenant_id + user_id
        if ($request->user()) {
            return sha1(
                $request->user()->tenant_id . '|' . 
                $request->user()->id . '|' . 
                $request->ip()
            );
        }

        // Sinon, utiliser l'IP uniquement
        return sha1($request->ip());
    }
}
