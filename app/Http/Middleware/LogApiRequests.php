<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Request Logger Middleware
 * 
 * Logs all API requests with performance metrics and response data
 */
class LogApiRequests
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Generate unique request ID for tracking
        $requestId = uniqid('req_', true);
        $request->attributes->set('request_id', $requestId);

        // Log incoming request
        $this->logRequest($request, $requestId);

        // Process request
        $response = $next($request);

        // Log response and performance
        $this->logResponse($request, $response, $requestId, $startTime);

        // Add request ID to response headers
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }

    /**
     * Log incoming request details
     */
    private function logRequest(Request $request, string $requestId): void
    {
        $context = [
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'tenant_id' => $request->user()?->tenant_id,
        ];

        // Add request payload for non-GET requests (exclude sensitive data)
        if (!$request->isMethod('get')) {
            $payload = $request->except(['password', 'password_confirmation', 'token', 'api_key']);
            if (!empty($payload)) {
                $context['payload'] = $payload;
            }
        }

        Log::channel('api')->info('API Request', $context);
    }

    /**
     * Log response and performance metrics
     */
    private function logResponse(Request $request, Response $response, string $requestId, float $startTime): void
    {
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $context = [
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'execution_time_ms' => $executionTime,
            'memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'user_id' => $request->user()?->id,
        ];

        // Log to performance channel if slow
        if ($executionTime > 1000) {
            Log::channel('performance')->warning('Slow API Request', $context);
        }

        // Log error responses
        if ($response->getStatusCode() >= 400) {
            $context['response_content'] = $response->getContent();
            Log::channel('api')->warning('API Error Response', $context);
        } else {
            Log::channel('api')->info('API Response', $context);
        }
    }
}
