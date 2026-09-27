<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');
Route::view('/umkm/profil', 'umkm.profil')->name('umkm.profil');
Route::view('/umkm/kebutuhan', 'umkm.kebutuhan')->name('umkm.kebutuhan');