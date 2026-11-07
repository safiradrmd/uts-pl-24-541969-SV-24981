<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

// Rute Publik
Route::post('/login', [AuthController::class, 'login']);

// Rute Terproteksi Token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/sales', [SaleController::class, 'index']);
    Route::post('/sales', [SaleController::class, 'store']);

    // Resource CRUD Products
    Route::apiResource('products', ProductController::class);
});

// Endpoint Healthcheck (Soal 3 UTS P10 - Ditaruh publik di luar middleware)
Route::get('/health', function () {
    $checks = [
        'database' => 'ok',
        'cache' => 'ok',
    ];
    $isHealthy = true;

    // 1. Cek Basis Data
    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        $checks['database'] = 'failed';
        $isHealthy = false;
        
        Log::error('Pemeriksaan kesehatan sistem: Basis Data Down', [
            'who' => 'system_healthcheck',
            'action' => 'check_database',
            'reason' => $e->getMessage(),
        ]);
    }

    // 2. Cek Cache
    try {
        Cache::put('health_check_key', true, 10);
        if (!Cache::get('health_check_key')) {
            throw new \Exception('Gagal membaca dari Cache');
        }
    } catch (\Exception $e) {
        $checks['cache'] = 'failed';
        $isHealthy = false;

        Log::error('Pemeriksaan kesehatan sistem: Cache Error', [
            'who' => 'system_healthcheck',
            'action' => 'check_cache',
            'reason' => $e->getMessage(),
        ]);
    }

    $statusCode = $isHealthy ? 200 : 503;

    return response()->json([
        'status' => $isHealthy ? 'healthy' : 'unhealthy',
        'checks' => $checks,
        'timestamp' => now()->toIso8601String(),
    ], $statusCode);
});