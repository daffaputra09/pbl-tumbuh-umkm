<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetugasUmkmController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');
Route::view('/umkm/profil', 'umkm.profil')->name('umkm.profil');
Route::view('/umkm/kebutuhan', 'umkm.kebutuhan')->name('umkm.kebutuhan');

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route::middleware('auth')->group(function () {
Route::get('/petugas/dashboard', [AuthController::class, 'dashboardPetugas'])->name('petugas.dashboard');
Route::get('/pimpinan/dashboard', [AuthController::class, 'dashboardPimpinan'])->name('pimpinan.dashboard');
Route::get('/umkm/dashboard', [AuthController::class, 'dashboardUmkm'])->name('umkm.dashboard');
// });

Route::get('/petugas/umkm', [PetugasUmkmController::class, 'verifikasi'])->name('petugas.umkm.verifikasi');
