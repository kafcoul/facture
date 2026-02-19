<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSanctum
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupérer le token depuis le header Authorization
        $token = $request->bearerToken();
        
        if (! $token) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }
        
        // Vérifier le token dans la table personal_access_tokens
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (! $accessToken) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }
        
        // Récupérer l'utilisateur depuis le token
        $user = $accessToken->tokenable;
        
        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }
        
        // Mettre à jour last_used_at
        $accessToken->forceFill(['last_used_at' => now()])->save();
        
        // Stocker le token dans la requête pour logout
        $request->attributes->set('sanctum_token', $accessToken);
        
        // Injecter l'utilisateur dans la requête
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
