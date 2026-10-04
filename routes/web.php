<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetugasUmkmController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\BusinessProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::middleware(['auth', 'active', 'role:'.User::ROLE_BUSINESS_OWNER])->group(function () {
    Route::get('/umkm/profil', [BusinessProfileController::class, 'edit'])->name('umkm.profil');
    Route::post('/umkm/profil', [BusinessProfileController::class, 'save'])->name('umkm.profil.save');
    
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

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/akun', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/akun', [AccountController::class, 'update'])->name('account.update');
});

Route::middleware(['auth', 'active', 'role:'.User::ROLE_OFFICER])->group(function () {
    Route::get('/petugas/dashboard', [AuthController::class, 'dashboardPetugas'])->name('petugas.dashboard');
    Route::get('/petugas/umkm', [PetugasUmkmController::class, 'verifikasi'])->name('petugas.umkm.verifikasi');

    Route::get('/petugas/umkm/pendataan-umkm', [BusinessProfileController::class, 'officerEdit'])->name('petugas.umkm.pendataan-umkm');
    Route::post('/petugas/umkm/pendataan-umkm', [BusinessProfileController::class, 'officerSave'])->name('petugas.umkm.pendataan-umkm.save');

    Route::get('/petugas/umkm/{id}', [PetugasUmkmController::class, 'detail'])->name('petugas.umkm.detail');
    Route::post('/petugas/umkm/{id}/verifikasi', [PetugasUmkmController::class, 'prosesVerifikasi'])->name('petugas.umkm.proses-verifikasi');
    Route::post('/petugas/umkm/{id}/tolak', [PetugasUmkmController::class, 'prosesTolak'])->name('petugas.umkm.proses-tolak');

    Route::view('/petugas/jenis-usaha', 'petugas.umkm.jenis-usaha')->name('petugas.jenis-usaha');

    Route::prefix('api/business-types')->name('api.business-types.')->group(function () {
        Route::get('/', [BusinessTypeController::class, 'index'])->name('index');
        Route::post('/', [BusinessTypeController::class, 'store'])->name('store');
        Route::put('/{businessType}', [BusinessTypeController::class, 'update'])->name('update');
        Route::patch('/{businessType}/toggle', [BusinessTypeController::class, 'toggle'])->name('toggle');
        Route::get('/petugas/jenis-usaha', [BusinessTypeController::class, 'index']);
    });

});

Route::middleware(['auth', 'active', 'role:'.User::ROLE_VILLAGE_HEAD])->group(function () {
    Route::get('/pimpinan/dashboard', [AuthController::class, 'dashboardPimpinan'])->name('pimpinan.dashboard');
});
