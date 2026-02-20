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
     * @OA\Get(
     *     path="/health",
     *     summary="Vérification de santé basique",
     *     description="Retourne OK si l'application tourne",
     *     tags={"Health"},
     *     @OA\Response(
     *         response=200,
     *         description="Application en ligne",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="ok"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="version", type="string", example="1.0.0")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/health/detailed",
     *     summary="Vérification de santé détaillée",
     *     description="Vérifie base de données, cache, storage et queue",
     *     tags={"Health"},
     *     @OA\Response(
     *         response=200,
     *         description="Tous les composants sont sains",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="healthy"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="environment", type="string", example="production"),
     *             @OA\Property(property="checks", type="object",
     *                 @OA\Property(property="database", type="object",
     *                     @OA\Property(property="status", type="string", example="healthy"),
     *                     @OA\Property(property="response_time_ms", type="number", example=1.5)
     *                 ),
     *                 @OA\Property(property="cache", type="object",
     *                     @OA\Property(property="status", type="string", example="healthy")
     *                 ),
     *                 @OA\Property(property="storage", type="object",
     *                     @OA\Property(property="status", type="string", example="healthy")
     *                 ),
     *                 @OA\Property(property="queue", type="object",
     *                     @OA\Property(property="status", type="string", example="healthy")
     *                 )
     *             ),
     *             @OA\Property(property="metrics", type="object",
     *                 @OA\Property(property="execution_time_ms", type="number", example=12.5),
     *                 @OA\Property(property="memory_usage_mb", type="number", example=32.5),
     *                 @OA\Property(property="memory_peak_mb", type="number", example=40.2)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=503, description="Un ou plusieurs composants sont en erreur")
     * )
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
     * @OA\Get(
     *     path="/metrics",
     *     summary="Métriques système pour tableaux de bord de monitoring",
     *     tags={"Health"},
     *     @OA\Response(
     *         response=200,
     *         description="Métriques système complètes",
     *         @OA\JsonContent(
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="system", type="object",
     *                 @OA\Property(property="php_version", type="string", example="8.4.17"),
     *                 @OA\Property(property="laravel_version", type="string", example="10.48.25")
     *             ),
     *             @OA\Property(property="memory", type="object",
     *                 @OA\Property(property="current_mb", type="number", example=32.5),
     *                 @OA\Property(property="peak_mb", type="number", example=40.2),
     *                 @OA\Property(property="limit", type="string", example="256M")
     *             ),
     *             @OA\Property(property="database", type="object"),
     *             @OA\Property(property="cache", type="object"),
     *             @OA\Property(property="queue", type="object",
     *                 @OA\Property(property="driver", type="string", example="database"),
     *                 @OA\Property(property="failed_jobs", type="integer", example=0)
     *             ),
     *             @OA\Property(property="storage", type="object")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/health/ready",
     *     summary="Sonde de disponibilité (readiness probe)",
     *     description="Pour Kubernetes/orchestration de conteneurs : vérifie que l'application est prête à recevoir du trafic",
     *     tags={"Health"},
     *     @OA\Response(
     *         response=200,
     *         description="Prêt",
     *         @OA\JsonContent(
     *             @OA\Property(property="ready", type="boolean", example=true),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="reasons", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=503, description="Non prêt — raisons fournies dans le body")
     * )
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
     * @OA\Get(
     *     path="/health/alive",
     *     summary="Sonde de vivacité (liveness probe)",
     *     description="Pour Kubernetes/orchestration de conteneurs : l'application est en cours d'exécution",
     *     tags={"Health"},
     *     @OA\Response(
     *         response=200,
     *         description="Vivant",
     *         @OA\JsonContent(
     *             @OA\Property(property="alive", type="boolean", example=true),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     )
     * )
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
