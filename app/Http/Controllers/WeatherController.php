<?php

namespace App\Http\Controllers;

use App\Models\EnvironmentalData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeatherController extends Controller
{
    /**
     * Display a listing of the environmental data.
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = EnvironmentalData::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('device_id', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'recorded_at_desc');

        switch ($sortBy) {
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'recorded_at_asc':
                $query->orderBy('recorded_at', 'asc');
                break;
            case 'recorded_at_desc':
                $query->orderBy('recorded_at', 'desc');
                break;
            case 'temperature_asc':
                $query->orderBy('temperature', 'asc');
                break;
            case 'temperature_desc':
                $query->orderBy('temperature', 'desc');
                break;
            case 'humidity_asc':
                $query->orderBy('humidity', 'asc');
                break;
            case 'humidity_desc':
                $query->orderBy('humidity', 'desc');
                break;
            case 'pressure_asc':
                $query->orderBy('pressure', 'asc');
                break;
            case 'pressure_desc':
                $query->orderBy('pressure', 'desc');
                break;
            default:
                $query->orderBy('recorded_at', 'desc');
        }

        // Calculate statistics before pagination
        $stats = $this->calculateStatistics($request);

        // Determine pagination mode
        $pagination = $request->get('pagination', '10');

        if ($pagination === 'all') {
            // Scenario 2: Load all data without pagination
            $environmentalData = $query->get();
        } else {
            // Scenario 1: Server-side pagination
            $perPage = is_numeric($pagination) ? (int)$pagination : 10;
            $environmentalData = $query->paginate($perPage)->withQueryString();
        }

        // FIXED: Explicitly pass all variables to view
        return view('weather.index', [
            'environmentalData' => $environmentalData,
            'totalRecords' => $stats['totalRecords'],
            'avgTemperature' => $stats['avgTemperature'],
            'avgHumidity' => $stats['avgHumidity'],
            'activeDevices' => $stats['activeDevices'],
        ]);
    }

    /**
     * Calculate statistics for the dashboard cards
     */
    private function calculateStatistics(Request $request)
    {
        $query = EnvironmentalData::query();

        // Apply same search filter for consistent stats
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('device_id', 'like', "%{$search}%");
        }

        $stats = $query->select(
            DB::raw('COUNT(*) as total_records'),
            DB::raw('AVG(temperature) as avg_temperature'),
            DB::raw('AVG(humidity) as avg_humidity'),
            DB::raw('COUNT(DISTINCT device_id) as active_devices')
        )->first();

        return [
            'totalRecords' => $stats->total_records ?? 0,
            'avgTemperature' => round($stats->avg_temperature ?? 0, 1),
            'avgHumidity' => round($stats->avg_humidity ?? 0, 1),
            'activeDevices' => $stats->active_devices ?? 0,
        ];
    }

    /**
     * Export environmental data
     */
    public function export(Request $request)
    {
        // Implementation for CSV/Excel export
        $query = EnvironmentalData::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('device_id', 'like', "%{$search}%");
        }

        // Apply sorting if needed
        if ($request->filled('sort_by')) {
            $sortBy = $request->sort_by;

            switch ($sortBy) {
                case 'temperature_desc':
                    $query->orderBy('temperature', 'desc');
                    break;
                case 'temperature_asc':
                    $query->orderBy('temperature', 'asc');
                    break;
                case 'humidity_desc':
                    $query->orderBy('humidity', 'desc');
                    break;
                case 'humidity_asc':
                    $query->orderBy('humidity', 'asc');
                    break;
                default:
                    $query->orderBy('recorded_at', 'desc');
            }
        }

        $data = $query->get();

        // Generate CSV
        $filename = 'environmental_data_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Device ID',
                'Temperature (°C)',
                'Humidity (%)',
                'Pressure (hPa)',
                'Light Level',
                'Recorded At'
            ]);

            // Add data rows
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->device_id,
                    number_format($row->temperature, 2),
                    number_format($row->humidity, 2),
                    number_format($row->pressure, 2),
                    number_format($row->light_level, 2),
                    $row->recorded_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
