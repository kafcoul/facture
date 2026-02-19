<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * Health Check Controller
 * 
 * Provides detailed health status and metrics for monitoring systems
 */
class HealthCheckController extends Controller
{
    /**
     * Basic health check endpoint
     * 
     * Quick check that returns OK if the application is running
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
        ]);
    }

    /**
     * Detailed health check with all system components
     * 
     * Checks database, cache, storage, queue, and more
     */
    public function detailed(): JsonResponse
    {
        $startTime = microtime(true);

        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];

        $allHealthy = collect($checks)->every(fn($check) => $check['status'] === 'healthy');

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'checks' => $checks,
            'metrics' => [
                'execution_time_ms' => $executionTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ],
        ], $allHealthy ? 200 : 503);
    }

    /**
     * System metrics endpoint for monitoring dashboards
     */
    public function metrics(): JsonResponse
    {
        return response()->json([
            'timestamp' => now()->toIso8601String(),
            'system' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server' => [
                    'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
                    'hostname' => gethostname(),
                ],
            ],
            'memory' => [
                'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'limit' => ini_get('memory_limit'),
            ],
            'database' => $this->getDatabaseMetrics(),
            'cache' => [
                'driver' => config('cache.default'),
                'prefix' => config('cache.prefix'),
            ],
            'queue' => [
                'driver' => config('queue.default'),
                'failed_jobs' => DB::table('failed_jobs')->count(),
            ],
            'storage' => [
                'default_disk' => config('filesystems.default'),
                'available_disks' => config('filesystems.disks') ? array_keys(config('filesystems.disks')) : [],
            ],
        ]);
    }

    /**
     * Readiness probe for Kubernetes/container orchestration
     */
    public function ready(): JsonResponse
    {
        $ready = true;
        $reasons = [];

        // Check database connection
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $ready = false;
            $reasons[] = 'database connection failed';
        }

        // Check if migrations are up to date (optional)
        // You can add more readiness checks here

        return response()->json([
            'ready' => $ready,
            'timestamp' => now()->toIso8601String(),
            'reasons' => $reasons,
        ], $ready ? 200 : 503);
    }

    /**
     * Liveness probe for Kubernetes/container orchestration
     */
    public function alive(): JsonResponse
    {
        return response()->json([
            'alive' => true,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Check database health
     */
    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            
            // Test a simple query
            $result = DB::select('SELECT 1 as test');
            $responseTime = round((microtime(true) - $start) * 1000, 2);

            // Get connection stats
            $stats = [
                'connection' => config('database.default'),
                'driver' => config('database.connections.'.config('database.default').'.driver'),
            ];

            return [
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'message' => 'Database connection successful',
                'details' => $stats,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache health
     */
    private function checkCache(): array
    {
        try {
            $start = microtime(true);
            $testKey = 'health_check_' . time();
            $testValue = 'ok';

            // Test cache write
            Cache::put($testKey, $testValue, 10);
            
            // Test cache read
            $retrieved = Cache::get($testKey);
            
            // Clean up
            Cache::forget($testKey);

            $responseTime = round((microtime(true) - $start) * 1000, 2);

            if ($retrieved !== $testValue) {
                throw new \Exception('Cache read/write mismatch');
            }

            return [
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'message' => 'Cache read/write successful',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Cache operation failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage health
     */
    private function checkStorage(): array
    {
        try {
            $start = microtime(true);
            $disk = Storage::disk(config('filesystems.default'));
            
            // Test write
            $testFile = 'health_check_' . time() . '.txt';
            $disk->put($testFile, 'health check test');
            
            // Test read
            $content = $disk->get($testFile);
            
            // Clean up
            $disk->delete($testFile);

            $responseTime = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'message' => 'Storage read/write successful',
                'disk' => config('filesystems.default'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Storage operation failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check queue health
     */
    private function checkQueue(): array
    {
        try {
            $start = microtime(true);
            
            // Get failed jobs count
            $failedCount = DB::table('failed_jobs')->count();
            
            $responseTime = round((microtime(true) - $start) * 1000, 2);

            $status = $failedCount > 100 ? 'degraded' : 'healthy';

            return [
                'status' => $status,
                'response_time_ms' => $responseTime,
                'message' => 'Queue system operational',
                'driver' => config('queue.default'),
                'failed_jobs' => $failedCount,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Queue check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get database metrics
     */
    private function getDatabaseMetrics(): array
    {
        try {
            $tables = ['invoices', 'clients', 'products', 'payments', 'users'];
            $counts = [];

            foreach ($tables as $table) {
                try {
                    $counts[$table] = DB::table($table)->count();
                } catch (\Exception $e) {
                    $counts[$table] = 'error';
                }
            }

            return [
                'connection' => config('database.default'),
                'driver' => config('database.connections.'.config('database.default').'.driver'),
                'tables' => $counts,
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
