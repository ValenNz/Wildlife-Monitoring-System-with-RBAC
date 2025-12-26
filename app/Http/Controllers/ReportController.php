<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Display all reports
     */
    public function index(Request $request)
    {
        // Ambil semua laporan yang sudah dibuat
        $query = DB::table('reports')
            ->leftJoin('users', 'reports.generated_by', '=', 'users.id')
            ->select(
                'reports.*',
                'users.name as generated_by_name'
            )
            ->orderBy('reports.generated_at', 'desc');

        // Filter berdasarkan tipe
        if ($request->filled('type')) {
            $query->where('reports.report_type', $request->type);
        }

        // Pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reports.title', 'LIKE', "%{$request->search}%")
                  ->orWhere('reports.report_type', 'LIKE', "%{$request->search}%");
            });
        }

        $reports = $query->paginate(10);

        // Statistics
        $totalAnimals = DB::table('animals')->count();
        $activeDevices = DB::table('devices')->where('status', 'active')->count();
        $totalTracking = DB::table('tracking_data')->count();
        $totalEnvironmental = DB::table('environmental_data')->count();

        // Recent activity
        $trackingToday = DB::table('tracking_data')
            ->where('recorded_at', '>=', Carbon::today())
            ->count();

        $trackingThisWeek = DB::table('tracking_data')
            ->where('recorded_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        $trackingThisMonth = DB::table('tracking_data')
            ->where('recorded_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        // Total reports
        $totalReports = DB::table('reports')->count();

        return view('reports.index', compact(
            'reports',
            'totalReports',
            'totalAnimals',
            'activeDevices',
            'totalTracking',
            'totalEnvironmental',
            'trackingToday',
            'trackingThisWeek',
            'trackingThisMonth'
        ));
    }

    /**
     * Show report generation form
     */
    public function create(Request $request)
{
    $animals = DB::table('animals')
        ->select('id', 'name', 'species')
        ->orderBy('name')
        ->get();

    // Ambil tipe laporan dari URL parameter
    $reportType = $request->get('report_type', 'activity');

    $reportTypes = [
        'activity' => 'Animal Activity Report',
        'device' => 'Device Performance Report',
        'environmental' => 'Environmental Report',
        'incident' => 'Incident Report'
    ];

    return view('reports.create', compact('animals', 'reportTypes', 'reportType'));
}

    /**
     * Generate and store report
     */
    public function store(Request $request)
{
    try {
        $request->validate([
            'report_type' => 'required|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'title' => 'required|string|max:255',
        ]);

        $reportData = $this->generateReportData($request->all());

        $reportId = DB::table('reports')->insertGetId([
            'title' => $request->title,
            'report_type' => $request->report_type,
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'period_start' => $request->date_from,
            'period_end' => $request->date_to,
            'content' => json_encode($reportData, JSON_UNESCAPED_UNICODE),
            'metadata' => json_encode([
                'filters' => $request->only(['animal_id', 'date_from', 'date_to']),
                'generated_at' => now()->toDateTimeString(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('reports.show', $reportId)
            ->with('success', 'Report generated successfully!');

    } catch (\Exception $e) {
        \Log::error('Report generation failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to generate report. Please try with a smaller date range.');
    }
}

    /**
     * Display specific report
     */
    public function show($id)
    {
        $report = DB::table('reports')
            ->leftJoin('users', 'reports.generated_by', '=', 'users.id')
            ->select('reports.*', 'users.name as generated_by_name')
            ->where('reports.id', $id)
            ->first();

        if (!$report) {
            return redirect()->route('reports.index')->with('error', 'Report not found');
        }

        $reportData = json_decode($report->content, true) ?? [];
        $metadata = json_decode($report->metadata, true) ?? [];

        return view('reports.show', compact('report', 'reportData', 'metadata'));
    }

    /**
     * Export report to CSV
     */
    public function export($id)
    {
        $report = DB::table('reports')
            ->where('id', $id)
            ->first();

        if (!$report) {
            return redirect()->back()->with('error', 'Report not found');
        }

        $reportData = json_decode($report->content, true) ?? [];

        // Generate CSV berdasarkan tipe laporan
        if ($report->report_type === 'activity' && isset($reportData['movements_by_animal'])) {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . str_slug($report->title) . '.csv"',
            ];

            $callback = function() use ($reportData) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Animal Name', 'Species', 'Total Readings', 'Avg Speed', 'Max Speed']);

                foreach ($reportData['movements_by_animal'] as $animal) {
                    fputcsv($file, [
                        $animal->name ?? 'N/A',
                        $animal->species ?? 'N/A',
                        $animal->total_readings ?? 0,
                        number_format($animal->avg_speed ?? 0, 2),
                        number_format($animal->max_speed ?? 0, 2)
                    ]);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Export not available for this report type');
    }

    /**
     * Download report as PDF (placeholder - bisa diimplementasikan dengan DomPDF)
     */
    public function download($id)
    {
        $report = DB::table('reports')
            ->where('id', $id)
            ->first();

        if (!$report) {
            return redirect()->back()->with('error', 'Report not found');
        }

        // Placeholder - untuk implementasi PDF, gunakan DomPDF atau TCPDF
        return redirect()->route('reports.show', $id)
            ->with('info', 'PDF download will be implemented in next phase');
    }

    /**
     * Generate report on-the-fly (AJAX endpoint)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:activity,device,environmental,incident',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $reportData = $this->generateReportData($request->all());

        return response()->json([
            'success' => true,
            'report_data' => $reportData,
            'type' => $request->type,
            'date_range' => $request->date_from . ' to ' . $request->date_to
        ]);
    }

    /**
     * Generate Animal Activity Report
     */
    private function generateActivityReport($params)
{
    $dateFrom = $params['date_from'];
    $dateTo = $params['date_to'];

    $data = [];

    // ✅ Tambahkan LIMIT untuk mencegah timeout
    $data['movements_by_animal'] = DB::table('tracking_data as t')
        ->join('devices as d', 't.device_id', '=', 'd.device_id')
        ->join('animals as a', 'd.animal_id', '=', 'a.id')
        ->whereBetween('t.recorded_at', [$dateFrom, $dateTo])
        ->select(
            'a.name',
            'a.species',
            DB::raw('COUNT(*) as total_readings'),
            DB::raw('AVG(t.speed) as avg_speed'),
            DB::raw('MAX(t.speed) as max_speed')
        )
        ->groupBy('a.id', 'a.name', 'a.species')
        ->orderBy('total_readings', 'desc')
        ->limit(50) // ✅ Batasi hanya 50 satwa teraktif
        ->get();

    // Most active hours
    $data['activity_by_hour'] = DB::table('tracking_data')
        ->whereBetween('recorded_at', [$dateFrom, $dateTo])
        ->select(
            DB::raw('HOUR(recorded_at) as hour'),
            DB::raw('COUNT(*) as readings')
        )
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();

    return $data;
}

    /**
     * Generate Device Performance Report
     */
    private function generateDeviceReport($params)
    {
        $dateFrom = $params['date_from'];
        $dateTo = $params['date_to'];

        $data = [];

        $data['devices'] = DB::table('devices')
            ->select(
                'device_id',
                'status',
                'battery_level',
                'last_seen'
            )
            ->get();

        $data['transmission_stats'] = DB::table('tracking_data as t')
            ->join('devices as d', 't.device_id', '=', 'd.device_id')
            ->whereBetween('t.recorded_at', [$dateFrom, $dateTo])
            ->select(
                'd.device_id',
                DB::raw('COUNT(*) as total_transmissions')
            )
            ->groupBy('d.device_id')
            ->get();

        return $data;
    }

    /**
     * Generate Environmental Report
     */
    private function generateEnvironmentalReport($params)
    {
        $dateFrom = $params['date_from'];
        $dateTo = $params['date_to'];

        $data = [];

        try {
            $data['temperature'] = DB::table('environmental_data')
                ->whereBetween('recorded_at', [$dateFrom, $dateTo])
                ->select(
                    DB::raw('DATE(recorded_at) as date'),
                    DB::raw('AVG(temperature) as avg_temp'),
                    DB::raw('MIN(temperature) as min_temp'),
                    DB::raw('MAX(temperature) as max_temp')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $data['humidity'] = DB::table('environmental_data')
                ->whereBetween('recorded_at', [$dateFrom, $dateTo])
                ->select(
                    DB::raw('DATE(recorded_at) as date'),
                    DB::raw('AVG(humidity) as avg_humidity')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } catch (\Exception $e) {
            $data['error'] = 'Environmental data not available';
        }

        return $data;
    }

    /**
     * Generate Incident Report
     */
    private function generateIncidentReport($params)
    {
        $dateFrom = $params['date_from'];
        $dateTo = $params['date_to'];

        $data = [];

        $data['alerts'] = DB::table('notifications')
            ->where('type', 'error')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('id', 'title', 'message', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $data['alerts_by_type'] = DB::table('notifications')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                'type',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('type')
            ->get();

        return $data;
    }

    /**
     * Generate report data based on parameters
     */
    private function generateReportData($params)
    {
        $type = $params['report_type'];

        switch ($type) {
            case 'activity':
                return $this->generateActivityReport($params);
            case 'device':
                return $this->generateDeviceReport($params);
            case 'environmental':
                return $this->generateEnvironmentalReport($params);
            case 'incident':
                return $this->generateIncidentReport($params);
            case 'population':
                return $this->generatePopulationReport($params);
            case 'heatmap':
                return $this->generateHeatmapReport($params);
            default:
                return ['error' => 'Unknown report type'];
        }
    }

    /**
     * Generate Population Statistics Report
     */
    private function generatePopulationReport($params)
    {
        return [
            'population_by_species' => DB::table('animals')
                ->select('species', DB::raw('COUNT(*) as total'))
                ->groupBy('species')
                ->get(),
            'total_animals' => DB::table('animals')->count(),
            'active_animals' => DB::table('animals as a')
                ->join('devices as d', 'a.id', '=', 'd.animal_id')
                ->where('d.status', 'active')
                ->count()
        ];
    }

    /**
     * Generate Heatmap Analysis Report
     */
    private function generateHeatmapReport($params)
    {
        $dateFrom = $params['date_from'];
        $dateTo = $params['date_to'];

        return [
            'heatmap_points' => DB::table('tracking_data')
                ->whereBetween('recorded_at', [$dateFrom, $dateTo])
                ->select('latitude', 'longitude', DB::raw('COUNT(*) as intensity'))
                ->groupBy('latitude', 'longitude')
                ->limit(1000)
                ->get()
        ];
    }

    /**
     * Delete a report
     */
    public function destroy($id)
    {
        DB::table('reports')
            ->where('id', $id)
            ->where('generated_by', auth()->id())
            ->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully');
    }
}
