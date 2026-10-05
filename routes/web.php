<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PetugasUmkmController;
use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::middleware(['auth', 'active', 'role:'.User::ROLE_BUSINESS_OWNER, 'business.profile'])->group(function () {
    Route::get('/umkm/profil', [BusinessProfileController::class, 'edit'])->name('umkm.profil');
    Route::post('/umkm/profil', [BusinessProfileController::class, 'save'])->name('umkm.profil.save');

    Route::view('/umkm/kebutuhan', 'umkm.kebutuhan')->name('umkm.kebutuhan');
    Route::view('/umkm/dashboard', 'umkm.dashboard')->name('umkm.dashboard');

    Route::get('/umkm/produk', [ProductController::class, 'page'])->name('umkm.produk');
    Route::get('/umkm/produk/data', [ProductController::class, 'index'])->name('umkm.produk.index');
    Route::post('/umkm/produk', [ProductController::class, 'store'])->name('umkm.produk.store');
    Route::put('/umkm/produk/{product}', [ProductController::class, 'update'])->name('umkm.produk.update');
    Route::patch('/umkm/produk/{product}/toggle', [ProductController::class, 'toggle'])->name('umkm.produk.toggle');
});

Route::middleware(['auth', 'active', 'role:'.User::ROLE_OFFICER.','.User::ROLE_VILLAGE_HEAD])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
    Route::get('forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.update');
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

    Route::get('/petugas/umkm/{business}/produk', [ProductController::class, 'officerPage'])->name('petugas.umkm.produk');
    Route::get('/petugas/umkm/{business}/produk/data', [ProductController::class, 'officerIndex'])->name('petugas.umkm.produk.index');
    Route::post('/petugas/umkm/{business}/produk', [ProductController::class, 'officerStore'])->name('petugas.umkm.produk.store');
    Route::put('/petugas/umkm/{business}/produk/{product}', [ProductController::class, 'officerUpdate'])->name('petugas.umkm.produk.update');
    Route::patch('/petugas/umkm/{business}/produk/{product}/toggle', [ProductController::class, 'officerToggle'])->name('petugas.umkm.produk.toggle');

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
