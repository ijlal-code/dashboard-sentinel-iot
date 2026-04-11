<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Monitoring;

class MonitoringController extends Controller
{
    public function store(Request $request)
    {
        Monitoring::create([
            'jarak' => $request->jarak,
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function index()
    {
        return Monitoring::latest()->take(10)->get();
    }
}