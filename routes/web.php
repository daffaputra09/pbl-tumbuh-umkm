<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');
Route::view('/umkm/profil', 'umkm.profil')->name('umkm.profil');
Route::view('/umkm/kebutuhan', 'umkm.kebutuhan')->name('umkm.kebutuhan');
Route::get('/dashboard', DashboardController::class)->name('dashboard');
