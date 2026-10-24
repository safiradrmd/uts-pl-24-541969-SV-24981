<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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
