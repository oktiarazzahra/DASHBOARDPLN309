<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TarifDashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\DebugSyncController;
use App\Http\Controllers\Api\SyncStatusController;
use App\Http\Controllers\Api\TarifSyncStatusController;

// Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// Debug Routes - untuk cek data di database tanpa Shell access
Route::get('/debug-data', [DebugController::class, 'index'])->name('debug.data');
Route::get('/debug-sync', [DebugSyncController::class, 'sync'])->name('debug.sync');

// Simple test route
Route::get('/test', function() {
    return response()->json([
        'status' => 'OK',
        'message' => 'Route working!',
        'timestamp' => now(),
        'db_connection' => env('DB_CONNECTION'),
        'db_path' => env('DB_DATABASE'),
    ]);
});

Route::get('/tarif', [TarifDashboardController::class, 'index'])->name('dashboard.tarif');
Route::post('/sync-data', [DashboardController::class, 'syncData'])->name('dashboard.sync');

// API Routes untuk AJAX
Route::get('/api/monitoring-data', [DashboardController::class, 'getData'])->name('api.data');
Route::get('/api/statistics', [DashboardController::class, 'getStatistics'])->name('api.statistics');

// Real-time Sync Status API - Per ULP
Route::get('/api/sync-status', [SyncStatusController::class, 'status'])->name('api.sync.status');
Route::post('/api/trigger-sync', [SyncStatusController::class, 'triggerSync'])->name('api.sync.trigger');

// Real-time Sync Status API - Per Tarif
Route::get('/api/tarif/sync-status', [TarifSyncStatusController::class, 'status'])->name('api.tarif.sync.status');
Route::post('/api/tarif/trigger-sync', [TarifSyncStatusController::class, 'triggerSync'])->name('api.tarif.sync.trigger');
