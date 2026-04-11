<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MonitoringController;


Route::get('/', function () {
    return view('welcome');
});

// Tambahkan ini untuk membuka halaman dashboard
Route::get('/dashboard', function () {
    return view('monitoring');
});

Route::post('/kirim-data', [MonitoringController::class, 'store']);
Route::get('/monitoring', [MonitoringController::class, 'index']);

// Tambahkan baris ini untuk menerima klik tombol dari Web
Route::post('/buzzer/toggle', [MonitoringController::class, 'toggleBuzzer']);
