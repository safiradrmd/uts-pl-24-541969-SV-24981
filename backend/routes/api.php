<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Rute Publik
Route::post('/login', [AuthController::class, 'login']);

// Rute Terproteksi Token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Resource CRUD Products
    Route::apiResource('products', ProductController::class);
});