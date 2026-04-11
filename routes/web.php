<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Tambahkan ini untuk membuka halaman dashboard
Route::get('/dashboard', function () {
    return view('monitoring');
});