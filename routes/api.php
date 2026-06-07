<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\StorageLocationController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SettingController;

// ======================
// PUBLIC ROUTES
// ======================

Route::post('/login', [AuthController::class, 'login'])->name('login');

// Data master (public sementara)
Route::apiResource('items',             ItemController::class);
Route::apiResource('categories',        CategoryController::class);
Route::apiResource('suppliers',         SupplierController::class);
Route::apiResource('units',             UnitController::class);
Route::apiResource('storage-locations', StorageLocationController::class);

// ======================
// PROTECTED ROUTES
// ======================

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me',    [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile (semua role)
    Route::put('/profile',          [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);

    // Transaksi - admin & staff
    Route::middleware('role:admin,staff')->group(function () {
        Route::apiResource('transactions', TransactionController::class);
    });

    // Notifikasi - admin & staff
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('notifications',                          [NotificationController::class, 'index']);
        Route::patch('notifications/{notification}/read',    [NotificationController::class, 'markAsRead']);
        Route::patch('notifications/read-all',               [NotificationController::class, 'markAllAsRead']);
    });

    // Laporan - admin & manager
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('reports/stock',        [ReportController::class, 'stock']);
        Route::get('reports/transactions', [ReportController::class, 'transactions']);
        Route::get('reports/summary',      [ReportController::class, 'summary']);
        Route::get('reports/export',       [ReportController::class, 'export']);
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('backup', [BackupController::class, 'backup']);
        Route::post('restore', [BackupController::class, 'restore']);
        Route::apiResource('settings', SettingController::class);
        Route::apiResource('users',    UserController::class);
    });
});