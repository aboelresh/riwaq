<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $checks = [];
        $healthy = true;

        // Database check
        try {
            DB::connection()->getPdo();
            $dbTime = DB::selectOne('SELECT 1 as ok');
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