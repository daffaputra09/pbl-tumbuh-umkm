<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');
Route::view('/umkm/profil', 'umkm.profil')->name('umkm.profil');
