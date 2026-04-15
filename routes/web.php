<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MonitoringController;

Route::get('/dashboard', function () {
    return view('monitoring');
})->name('dashboard');

Route::get('/', function () {
    return redirect()->route('dashboard');
});