<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetugasUmkmController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::middleware(['auth', 'active', 'role:'.User::ROLE_BUSINESS_OWNER])->group(function () {
    Route::view('/umkm/profil', 'umkm.profil')->name('umkm.profil');
    Route::view('/umkm/kebutuhan', 'umkm.kebutuhan')->name('umkm.kebutuhan');
    Route::view('/umkm/dashboard', 'umkm.dashboard')->name('umkm.dashboard');
});

Route::middleware(['auth', 'active', 'role:'.User::ROLE_OFFICER.','.User::ROLE_VILLAGE_HEAD])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'active', 'role:'.User::ROLE_OFFICER])->group(function () {
    Route::get('/petugas/dashboard', [AuthController::class, 'dashboardPetugas'])->name('petugas.dashboard');
    Route::get('/petugas/umkm', [PetugasUmkmController::class, 'verifikasi'])->name('petugas.umkm.verifikasi');
});

Route::middleware(['auth', 'active', 'role:'.User::ROLE_VILLAGE_HEAD])->group(function () {
    Route::get('/pimpinan/dashboard', [AuthController::class, 'dashboardPimpinan'])->name('pimpinan.dashboard');
});
