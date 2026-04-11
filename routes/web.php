<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Membuka halaman dashboard
Route::get('/dashboard', function () {
    return view('monitoring');
});

// Route /kirim-data, /monitoring, dan /buzzer/toggle SUDAH DIHAPUS dari sini 
// karena sudah dipindahkan ke rute yang benar yaitu routes/api.php