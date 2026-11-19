<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Halaman Login & Logout
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route Asisten Praktikum
Route::middleware(['auth', 'role:asprak'])->prefix('asprak')->name('asprak.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'asprak'])->name('dashboard');
    Route::get('/jadwal', fn() => 'Halaman Jadwal Asprak (Day 4)')->name('jadwal');
    Route::get('/rps', fn() => 'Halaman RPS & Bahan Ajar Asprak (Day 5)')->name('rps');
    Route::get('/riwayat', fn() => 'Halaman Riwayat & Rekap Asprak (Day 6)')->name('riwayat');
});

// Route Dosen Koordinator
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dosen'])->name('dashboard');
    Route::get('/jadwal', fn() => 'Halaman Jadwal Dosen (Day 4)')->name('jadwal');
    Route::get('/monitoring', fn() => 'Halaman Monitoring Dosen (Day 7)')->name('monitoring');
});

// Route Koordinator Asprak
Route::middleware(['auth', 'role:koor_asprak'])->prefix('koor')->name('koor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'koor'])->name('dashboard');
    Route::get('/jadwal', fn() => 'Halaman Jadwal Koor Asprak (Day 4)')->name('jadwal');
    Route::get('/monitoring', fn() => 'Halaman Monitoring Koor Asprak (Day 7)')->name('monitoring');
    Route::get('/rps', fn() => 'Halaman RPS & Bahan Ajar Koor Asprak (Day 5)')->name('rps');
});