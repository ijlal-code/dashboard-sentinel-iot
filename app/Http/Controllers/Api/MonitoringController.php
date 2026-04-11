<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Monitoring;
use Illuminate\Support\Facades\Cache; // PENTING: Tambahkan ini untuk memori Buzzer

class MonitoringController extends Controller
{
    public function store(Request $request)
    {
        // Simpan data jarak dan status ke database
        Monitoring::create([
            'jarak' => $request->jarak,
            'status' => $request->status
        ]);

        // Cek status buzzer saat ini dari memori Cache (Default: ON)
        $buzzerState = Cache::get('buzzer_state', 'ON');

        // Kirim balasan ke ESP32 agar ESP32 tahu apakah buzzer boleh bunyi atau tidak
        return response()->json([
            'message' => 'Data berhasil disimpan',
            'buzzer_state' => $buzzerState 
        ]);
    }

    public function index()
    {
        // Tampilkan data ke Web sekaligus mengirim status Buzzer
        return response()->json([
            'data' => Monitoring::latest()->take(10)->get(),
            'buzzer_state' => Cache::get('buzzer_state', 'ON')
        ]);
    }

    // Fungsi baru untuk menangani klik tombol dari Web
    public function toggleBuzzer(Request $request)
    {
        $state = $request->state; // Akan menerima 'ON' atau 'OFF' dari Javascript Web
        
        if ($state === 'OFF') {
            // Jika diklik mati, simpan status OFF di Cache selama 20 Menit.
            // Setelah 20 menit, ini otomatis kadaluarsa dan kembali menjadi ON.
            Cache::put('buzzer_state', 'OFF', now()->addMinutes(20));
        } else {
            // Jika diklik nyala secara manual
            Cache::put('buzzer_state', 'ON');
        }

        return response()->json([
            'message' => 'Berhasil! Status Buzzer sekarang: ' . $state,
            'buzzer_state' => $state
        ]);
    }
}