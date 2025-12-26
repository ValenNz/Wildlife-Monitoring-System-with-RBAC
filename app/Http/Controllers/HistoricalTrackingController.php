<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoricalTrackingController extends Controller
{
    /**
     * Display a listing of GPS tracking data with search, sort, and pagination.
     */
    public function index(Request $request)
    {
        // Query dasar - join dengan devices untuk informasi perangkat
        $query = DB::table('tracking_data as t')
    ->join('devices as d', 't.device_id', '=', 'd.device_id')
            ->select(
                't.id',
                't.device_id',
                't.latitude',
                't.longitude',
                't.altitude',
                't.speed',
                't.heading',
                't.recorded_at',
                'd.status as device_status'
            );

        // Filter pencarian (device_id)
        if ($request->filled('search')) {
            $query->where('t.device_id', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'recorded_at_desc');
        switch ($sortBy) {
            case 'id_asc':
                $query->orderBy('t.id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('t.id', 'desc');
                break;
            case 'recorded_at_asc':
                $query->orderBy('t.recorded_at', 'asc');
                break;
            case 'recorded_at_desc':
                $query->orderBy('t.recorded_at', 'desc');
                break;
            case 'speed_asc':
                $query->orderBy('t.speed', 'asc');
                break;
            case 'speed_desc':
                $query->orderBy('t.speed', 'desc');
                break;
            case 'altitude_asc':
                $query->orderBy('t.altitude', 'asc');
                break;
            case 'altitude_desc':
                $query->orderBy('t.altitude', 'desc');
                break;
            default:
                $query->orderBy('t.recorded_at', 'desc');
        }

        // Hitung total records (untuk statistik)
        $totalRecords = $query->count();

        // Pagination
        $perPage = $request->get('per_page', 10);
        if ($perPage === 'all') {
            $trackingData = $query->get();
            $showPagination = false;
        } else {
            $perPage = is_numeric($perPage) && $perPage > 0 ? (int) $perPage : 10;
            $trackingData = $query->paginate($perPage)->appends($request->query());
            $showPagination = true;
        }

        return view('historical-tracking.index', compact(
            'trackingData',
            'totalRecords',
            'showPagination'
        ));
    }

    /**
     * Export tracking data to CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('wildlife_db.tracking_data')
            ->select('id', 'device_id', 'latitude', 'longitude', 'altitude', 'speed', 'heading', 'recorded_at');

        if ($request->filled('search')) {
            $query->where('device_id', 'like', '%' . $request->search . '%');
        }

        $data = $query->get();

        $filename = 'tracking_data_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'ID', 'Device ID', 'Latitude', 'Longitude',
                'Altitude (m)', 'Speed (km/h)', 'Heading (°)', 'Recorded At'
            ]);

            // Data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->device_id,
                    number_format($row->latitude, 6),
                    number_format($row->longitude, 6),
                    number_format($row->altitude, 1),
                    number_format($row->speed, 1),
                    number_format($row->heading, 1),
                    $row->recorded_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
