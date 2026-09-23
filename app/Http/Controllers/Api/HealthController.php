<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;


class HealthController extends BaseController
{
    #[OA\Get(
        path: '/health',
        summary: 'System health check',
        description: 'Checks database, cache, and storage. Returns 503 if any system is down.',
        tags: ['System'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All systems operational',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'healthy', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'checks',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'database',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                                        new OA\Property(property: 'driver', type: 'string', example: 'sqlite'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'cache',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                                        new OA\Property(property: 'driver', type: 'string', example: 'file'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'storage',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'app',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                                        new OA\Property(property: 'environment', type: 'string', example: 'local'),
                                        new OA\Property(property: 'php', type: 'string', example: '8.2.12'),
                                        new OA\Property(property: 'laravel', type: 'string', example: '12.0.0'),
                                        new OA\Property(property: 'version', type: 'string', example: '1.0.0'),
                                    ]
                                ),
                            ]
                        ),
                        new OA\Property(property: 'time', type: 'string', format: 'date-time'),
                    ]
                )
            ),
            new OA\Response(response: 503, description: 'One or more systems degraded'),
        ]
    )]


    public function check(): JsonResponse
    {
        $checks  = [];
        $healthy = true;

        // Database check
        try {
            DB::connection()->getPdo();
            DB::selectOne('SELECT 1 as ok');
            $checks['database'] = ['status' => 'ok', 'driver' => DB::getDriverName()];
        } catch (\Throwable $e) {
            $checks['database'] = ['status' => 'fail', 'error' => 'Cannot connect'];
            $healthy = false;
        }

        // Cache check
        try {
            Cache::put('health_check', true, 5);
            $cacheOk = Cache::get('health_check') === true;
            $checks['cache'] = ['status' => $cacheOk ? 'ok' : 'fail', 'driver' => config('cache.default')];
            if (!$cacheOk) $healthy = false;
        } catch (\Throwable $e) {
            $checks['cache'] = ['status' => 'fail', 'error' => 'Cannot write/read'];
            $healthy = false;
        }

        // Storage check
        try {
            $testFile = storage_path('app/health_check.txt');
            file_put_contents($testFile, 'ok');
            $storageOk = file_get_contents($testFile) === 'ok';
            @unlink($testFile);
            $checks['storage'] = ['status' => $storageOk ? 'ok' : 'fail'];
            if (!$storageOk) $healthy = false;
        } catch (\Throwable $e) {
            $checks['storage'] = ['status' => 'fail', 'error' => 'Cannot write'];
            $healthy = false;
        }

        $checks['app'] = [
            'status'      => 'ok',
            'environment' => config('app.env'),
            'debug'       => config('app.debug'),
            'version'     => '1.0.0',
            'php'         => PHP_VERSION,
            'laravel'     => app()->version(),
        ];

        return response()->json([
            'success' => $healthy,
            'healthy' => $healthy,
            'checks'  => $checks,
            'time'    => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }
}