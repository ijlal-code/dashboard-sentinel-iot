<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    // Hanya gunakan ini untuk menampilkan halaman dashboard
    public function index()
    {
        return view('monitoring');
    }
}