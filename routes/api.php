<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MonitoringController;

Route::post('/kirim-data', [MonitoringController::class, 'store']);
Route::get('/monitoring', [MonitoringController::class, 'index']);

// Tambahkan route ini di sini (API) agar bisa menerima request dari Frontend dan ESP32
Route::post('/buzzer/toggle', [MonitoringController::class, 'toggleBuzzer']);