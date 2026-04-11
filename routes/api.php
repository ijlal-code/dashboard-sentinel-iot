<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MonitoringController;

Route::post('/kirim-data', [MonitoringController::class, 'store']);
Route::get('/monitoring', [MonitoringController::class, 'index']);