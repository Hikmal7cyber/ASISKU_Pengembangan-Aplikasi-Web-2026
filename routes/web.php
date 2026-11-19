<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Halaman Login & Logout
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route Asisten Praktikum
Route::middleware(['auth', 'role:asprak'])->prefix('asprak')->name('asprak.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Berhasil Login sebagai ASPRAK: ' . auth()->user()->name . ' <form method="POST" action="'.route('logout').'">'.csrf_field().'<button type="submit">Logout</button></form>';
    })->name('dashboard');
});

// Route Dosen Koordinator
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Berhasil Login sebagai DOSEN: ' . auth()->user()->name . ' <form method="POST" action="'.route('logout').'">'.csrf_field().'<button type="submit">Logout</button></form>';
    })->name('dashboard');
});

// Route Koordinator Asprak
Route::middleware(['auth', 'role:koor_asprak'])->prefix('koor')->name('koor.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Berhasil Login sebagai KOOR ASPRAK: ' . auth()->user()->name . ' <form method="POST" action="'.route('logout').'">'.csrf_field().'<button type="submit">Logout</button></form>';
    })->name('dashboard');
});