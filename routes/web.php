<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

// Halaman Login & Logout
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route Asisten Praktikum
Route::middleware(['auth', 'role:asprak'])->prefix('asprak')->name('asprak.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'asprak'])->name('dashboard');
    Route::get('/jadwal', [ScheduleController::class, 'asprakIndex'])->name('jadwal');
    Route::get('/rps', [MaterialController::class, 'asprakIndex'])->name('rps');
    Route::get('/riwayat', fn() => 'Halaman Riwayat & Rekap Asprak (Day 6)')->name('riwayat');
});

// Route Dosen Koordinator
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dosen'])->name('dashboard');
    Route::get('/jadwal', [ScheduleController::class, 'dosenIndex'])->name('jadwal');
    Route::post('/jadwal', [ScheduleController::class, 'dosenStore'])->name('jadwal.store');
    Route::put('/jadwal/{schedule}', [ScheduleController::class, 'dosenUpdate'])->name('jadwal.update');
    Route::get('/monitoring', fn() => 'Halaman Monitoring Dosen (Day 7)')->name('monitoring');
});

// Route Koordinator Asprak
Route::middleware(['auth', 'role:koor_asprak'])->prefix('koor')->name('koor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'koor'])->name('dashboard');
    Route::get('/jadwal', [ScheduleController::class, 'koorIndex'])->name('jadwal');
    Route::put('/jadwal/{schedule}', [ScheduleController::class, 'koorUpdate'])->name('jadwal.update');
    Route::get('/monitoring', fn() => 'Halaman Monitoring Koor Asprak (Day 7)')->name('monitoring');
    
    // Kelola RPS & Bahan Ajar
    Route::get('/rps', [MaterialController::class, 'koorIndex'])->name('rps');
    Route::post('/rps/{course}/upload', [MaterialController::class, 'uploadRps'])->name('rps.upload');
    Route::post('/rps/material', [MaterialController::class, 'storeMaterial'])->name('rps.material.store');
    Route::put('/rps/material/{material}', [MaterialController::class, 'updateMaterial'])->name('rps.material.update');
    Route::delete('/rps/material/{material}', [MaterialController::class, 'destroyMaterial'])->name('rps.material.destroy');
});