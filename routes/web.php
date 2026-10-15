<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SkkniComplianceController;
use App\Http\Controllers\SparePartController;
use Illuminate\Support\Facades\Route;

// 1. Dashboard Pusat Data
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 2. Manajemen Komponen Pusat Data (Server, Network, Storage, Power, Cooling)
Route::resource('components', ComponentController::class);

// 3. Inventaris Suku Cadang Pusat Data & Penyesuaian Stok (Memicu Trigger)
Route::resource('spareparts', SparePartController::class);
Route::post('spareparts/{sparepart}/adjust-stock', [SparePartController::class, 'adjustStock'])->name('spareparts.adjust-stock');

// 4. Perencanaan & Log Realisasi Pemeliharaan (Stored Procedure, Commit & Rollback)
Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/', [MaintenanceController::class, 'index'])->name('index');
    Route::get('/schedule/create', [MaintenanceController::class, 'createSchedule'])->name('schedules.create');
    Route::post('/schedule', [MaintenanceController::class, 'storeSchedule'])->name('schedules.store');
    Route::get('/logs/create', [MaintenanceController::class, 'createLog'])->name('logs.create');
    Route::post('/logs', [MaintenanceController::class, 'storeLog'])->name('logs.store');
    Route::get('/logs/{log}', [MaintenanceController::class, 'showLog'])->name('logs.show');
});

// 5. Administrasi Backup & Verifikasi Pemulihan Bencana (Disaster Recovery)
Route::prefix('backup')->name('backup.')->group(function () {
    Route::get('/', [BackupController::class, 'index'])->name('index');
    Route::post('/create', [BackupController::class, 'createBackup'])->name('create');
    Route::post('/{backup}/test-restore', [BackupController::class, 'testRestore'])->name('test-restore');
    Route::get('/{backup}/download', [BackupController::class, 'download'])->name('download');
});

// 6. Laporan Perencanaan & Realisasi Pemeliharaan (Format Cetak Resmi)
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/print', [ReportController::class, 'printView'])->name('print');
});

// 7. Pusat Kepatuhan SKKNI & Laboratorium Pengujian Teknis Interaktif
Route::prefix('skkni-compliance')->name('skkni.')->group(function () {
    Route::get('/', [SkkniComplianceController::class, 'index'])->name('index');
    Route::post('/api/benchmark', [SkkniComplianceController::class, 'runBenchmark'])->name('benchmark');
    Route::post('/api/test-procedure', [SkkniComplianceController::class, 'testStoredProcedure'])->name('test-procedure');
    Route::post('/api/test-function', [SkkniComplianceController::class, 'testStoredFunction'])->name('test-function');
    Route::post('/api/test-transaction', [SkkniComplianceController::class, 'testTransactionSimulation'])->name('test-transaction');
});
