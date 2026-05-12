<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\StorageLocationController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Protected (butuh token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Kelola user — hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // Data master — hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('categories',       CategoryController::class);
        Route::apiResource('suppliers',        SupplierController::class);
        Route::apiResource('units',            UnitController::class);
        Route::apiResource('storage-locations', StorageLocationController::class);
    });
});