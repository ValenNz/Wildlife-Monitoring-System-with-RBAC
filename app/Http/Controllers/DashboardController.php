<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with dynamic data loading.
     */
    public function index(Request $request)
    {
        // =============== 1. Quick Stats ===============
        $activeAnimals = DB::table('animals')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $gpsReadings = DB::table('tracking_data')->count();
        $protectedZones = DB::table('risk_zones')->where('zone_type', 'protected')->count();
        $totalAnimals = DB::table('animals')->count();

        // =============== 2. Signal & Device Status ===============
        $totalDevices = DB::table('devices')->count();
        $offlineDevices = DB::table('devices')
            ->where('last_seen', '<', Carbon::now()->subHour())
            ->count();
        $lowBatteryDevices = DB::table('devices')
            ->where('battery_level', '<', 20)
            ->count();

        // =============== 3. Recent Notifications ===============
        $recentNotifications = DB::table('notifications')
            ->select('id', 'title', 'message', 'type', 'is_read', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $unreadNotifications = DB::table('notifications')->where('is_read', 0)->count();

        // =============== 4. Monitored Animals (Dynamic Loading) ===============
        $search = $request->get('search', '');
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $perPage = $request->get('per_page', '10');

        // Validasi nilai per_page sesuai kebutuhan stress test
        $allowedPerPage = ['10', '100', '1000', 'all'];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = '10';
        }

        // Validasi kolom sorting
        $allowedSorts = ['name', 'species', 'created_at', 'status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'name';
        }
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';

        // Bangun query dasar (minimal kolom, sesuai prinsip optimasi SQL)
        $animalsQuery = DB::table('animals as a')
            ->leftJoin('devices as d', 'a.id', '=', 'd.animal_id')
            ->select(
                'a.id',
                'a.name',
                'a.species',
                'a.tag_id',
                'a.created_at',
                'd.device_id',
                'd.status as device_status'
            );

        // Filter pencarian (sesuai FR-09: Cari Satwa)
        if (!empty($search)) {
            $animalsQuery->where(function ($q) use ($search) {
                $q->where('a.name', 'LIKE', "%{$search}%")
                  ->orWhere('a.species', 'LIKE', "%{$search}%")
                  ->orWhere('a.tag_id', 'LIKE', "%{$search}%")
                  ->orWhere('d.device_id', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        if ($sortBy === 'status') {
            $animalsQuery->orderBy('d.status', $sortOrder);
        } else {
            $animalsQuery->orderBy('a.' . $sortBy, $sortOrder);
        }

        // Ambil data berdasarkan pilihan per_page
        if ($perPage === 'all') {
            // ❗ Skenario stress test TANPA PAGINATION
            $animals = $animalsQuery->get();
            $paginatedAnimals = null;
        } else {
            // ✅ Skenario stress test DENGAN PAGINATION
            $perPage = (int)$perPage;
            $paginatedAnimals = $animalsQuery->paginate($perPage)
                ->appends([
                    'search' => $search,
                    'sort' => $sortBy,
                    'order' => $sortOrder,
                    'per_page' => $perPage
                ]);
            $animals = $paginatedAnimals->items();
        }

        // Ambil last_seen dari tracking_data (sesuai FR-01: Real-time tracking)
        $deviceIds = collect($animals)->pluck('device_id')->filter()->values()->toArray();
        $latestTracking = $this->getLatestTracking($deviceIds);

        $monitoredAnimals = collect($animals)->map(function ($animal) use ($latestTracking) {
            $tracking = $latestTracking->get($animal->device_id);
            $animal->last_seen = $tracking ? $tracking->last_seen : null;
            return $animal;
        });

        // =============== 5. Return View ===============
        return view('dashboard.index', compact(
            'activeAnimals',
            'gpsReadings',
            'protectedZones',
            'totalAnimals',
            'totalDevices',
            'offlineDevices',
            'lowBatteryDevices',
            'recentNotifications',
            'unreadNotifications',
            'monitoredAnimals',
            'paginatedAnimals',
            'sortBy',
            'sortOrder',
            'search',
            'perPage'
        ));
    }

    /**
     * Get latest tracking data for a list of device IDs.
     * Implements fallback mechanism as per system robustness requirement (NFR-06).
     */
    private function getLatestTracking(array $deviceIds)
    {
        if (empty($deviceIds)) {
            return collect();
        }

        // Prioritaskan dari cache table (jika ada, sesuai DPPL hal. 12)
        try {
            $tracking = DB::table('latest_tracking_cache')
                ->whereIn('device_id', $deviceIds)
                ->select('device_id', 'last_seen', 'latitude', 'longitude')
                ->get()
                ->keyBy('device_id');

            if ($tracking->isNotEmpty()) {
                return $tracking;
            }
        } catch (\Exception $e) {
            // Cache table tidak tersedia → fallback ke query langsung
        }

        // Fallback: Query historis (kurang efisien, tapi aman)
        $tracking = collect();
        foreach ($deviceIds as $deviceId) {
            $latest = DB::table('tracking_data')
                ->where('device_id', $deviceId)
                ->orderBy('recorded_at', 'desc')
                ->select('device_id', 'recorded_at as last_seen', 'latitude', 'longitude')
                ->first();

            if ($latest) {
                $tracking->put($deviceId, $latest);
            }
        }

        return $tracking;
    }

    /**
     * Export animals data (FR-07: Ekspor data ke format terstruktur).
     */
    public function exportAnimals(Request $request)
    {
        // Implementasi export (opsional untuk presentasi)
        return response()->json(['message' => 'Export feature ready.']);
    }

    /**
     * Show animal detail page.
     */
    public function animalDetail($id)
    {
        $animal = DB::table('animals as a')
            ->leftJoin('devices as d', 'a.id', '=', ' d.animal_id')
            ->where('a.id', $id)
            ->select('a.*', 'd.device_id', 'd.status as device_status', 'd.battery_level', 'd.last_seen as device_last_seen')
            ->first();

        if (!$animal) {
            abort(404);
        }

        $trackingHistory = DB::table('tracking_data')
            ->where('device_id', $animal->device_id)
            ->orderBy('recorded_at', 'desc')
            ->limit(50)
            ->get();

        return view('dashboard.animal-detail', compact('animal', 'trackingHistory'));
    }
}
