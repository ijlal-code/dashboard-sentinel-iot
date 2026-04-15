<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Membuka halaman dashboard
Route::get('/dashboard', function () {
    return view('monitoring');
});

