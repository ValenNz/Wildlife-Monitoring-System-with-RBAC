<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function getLatestPositions()
    {
        // Ambil posisi terakhir dari semua perangkat aktif
        $positions = DB::select("
            SELECT device_id, latitude, longitude, recorded_at
            FROM gps_readings g1
            WHERE g1.recorded_at = (
                SELECT MAX(g2.recorded_at)
                FROM gps_readings g2
                WHERE g2.device_id = g1.device_id
            )
        ");

        return response()->json($positions);
    }
}
