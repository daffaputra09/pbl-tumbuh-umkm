<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetugasUmkmController;

Route::view('/', 'landing')->name('landing');
Route::view('/umkm/profil', 'umkm.profil')->name('umkm.profil');
Route::view('/umkm/kebutuhan', 'umkm.kebutuhan')->name('umkm.kebutuhan');
Route::get('/petugas/umkm', [PetugasUmkmController::class, 'verifikasi'])->name('petugas.umkm.verifikasi');
