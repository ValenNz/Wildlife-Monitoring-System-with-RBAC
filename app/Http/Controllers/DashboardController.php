<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display Dashboard
     */
    public function index(Request $request)
    {
        // 1️⃣ QUICK STATS
        $activeAnimals = DB::table('animals')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $gpsReadings = DB::table('tracking_data')->count();
        $protectedZones = DB::table('risk_zones')->where('zone_type', 'protected')->count();

        // 2️⃣ SIGNAL STATUS
        $totalDevices = DB::table('devices')->count();
        $offlineDevices = DB::table('devices')
            ->where('last_seen', '<', Carbon::now()->subHour())
            ->count();
        $lowBatteryDevices = DB::table('devices')
            ->where('battery_level', '<', 20)
            ->count();

        // 3️⃣ NOTIFICATIONS
        $recentNotifications = DB::table('notifications')
            ->select('id', 'title', 'message', 'type', 'is_read', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $unreadNotifications = DB::table('notifications')
            ->where('is_read', 0)
            ->count();

        // 4️⃣ MONITORED ANIMALS
        $search = $request->get('search', '');
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $perPage = $request->get('per_page', 10);

        $allowedSorts = ['name', 'species', 'created_at', 'status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'name';
        }

        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';

        $animalsQuery = DB::table('animals as a')
            ->leftJoin('devices as d', 'a.id', '=', 'd.animal_id')
            ->select('a.id', 'a.name', 'a.species', 'a.tag_id', 'a.created_at',
                     'd.id as device_pk', 'd.device_id', 'd.status as device_status');

        if (!empty($search)) {
            $animalsQuery->where(function($query) use ($search) {
                $query->where('a.name', 'LIKE', "%{$search}%")
                      ->orWhere('a.species', 'LIKE', "%{$search}%")
                      ->orWhere('a.tag_id', 'LIKE', "%{$search}%")
                      ->orWhere('d.device_id', 'LIKE', "%{$search}%");
            });
        }

        if ($sortBy === 'status') {
            $animalsQuery->orderBy('d.status', $sortOrder);
        } else {
            $animalsQuery->orderBy('a.' . $sortBy, $sortOrder);
        }

        if ($perPage === 'all') {
            $animals = $animalsQuery->get();
            $paginatedAnimals = null;
        } else {
            $perPage = is_numeric($perPage) ? (int)$perPage : 10;
            $perPage = max(5, min($perPage, 100));

            $paginatedAnimals = $animalsQuery->paginate($perPage)
                ->appends(['search' => $search, 'sort' => $sortBy, 'order' => $sortOrder, 'per_page' => $perPage]);

            $animals = $paginatedAnimals->items();
        }

        $deviceIds = collect($animals)->pluck('device_id')->filter()->toArray();
        $latestTracking = $this->getLatestTracking($deviceIds);

        $monitoredAnimals = collect($animals)->map(function($animal) use ($latestTracking) {
            $tracking = $latestTracking->get($animal->device_id);
            $animal->last_seen = $tracking->last_seen ?? null;
            $animal->latitude = $tracking->latitude ?? null;
            $animal->longitude = $tracking->longitude ?? null;
            return $animal;
        });

        if ($sortBy === 'last_seen') {
            $monitoredAnimals = $sortOrder === 'asc'
                ? $monitoredAnimals->sortBy('last_seen')->values()
                : $monitoredAnimals->sortByDesc('last_seen')->values();
        }

        // 5️⃣ ADDITIONAL STATS
        $animalsBySpecies = DB::table('animals')
            ->select('species', DB::raw('count(*) as total'))
            ->groupBy('species')
            ->limit(5)
            ->get();

        $recentActivities = DB::table('tracking_data')
            ->where('recorded_at', '>=', Carbon::now()->subDay())
            ->count();

        $criticalAlerts = DB::table('notifications')
            ->where('type', 'error')
            ->where('is_read', 0)
            ->count();

        $totalAnimals = DB::table('animals')->count();

        return view('dashboard.index', compact(
            'activeAnimals', 'gpsReadings', 'protectedZones',
            'totalDevices', 'offlineDevices', 'lowBatteryDevices',
            'recentNotifications', 'unreadNotifications', 'criticalAlerts',
            'monitoredAnimals', 'paginatedAnimals',
            'sortBy', 'sortOrder', 'search', 'perPage', 'totalAnimals',
            'animalsBySpecies', 'recentActivities'
        ));
    }

    /**
     * Export animals data
     */
    public function exportAnimals(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');

        $query = DB::table('animals as a')
            ->leftJoin('devices as d', 'a.id', '=', 'd.animal_id')
            ->select('a.id', 'a.name', 'a.species', 'a.tag_id', 'a.created_at',
                     'd.device_id', 'd.status as device_status');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('a.name', 'LIKE', "%{$search}%")
                  ->orWhere('a.species', 'LIKE', "%{$search}%")
                  ->orWhere('a.tag_id', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('a.' . $sortBy, $sortOrder);
        $animals = $query->get();

        return response()->json([
            'success' => true,
            'total' => $animals->count(),
            'exported_at' => Carbon::now()->toDateTimeString(),
            'data' => $animals
        ]);
    }

    /**
     * Animal detail page
     */
    public function animalDetail($id)
    {
        $animal = DB::table('animals as a')
            ->leftJoin('devices as d', 'a.id', '=', 'd.animal_id')
            ->where('a.id', $id)
            ->select('a.*', 'd.id as device_pk', 'd.device_id', 'd.status as device_status',
                     'd.battery_level', 'd.last_seen as device_last_seen')
            ->first();

        if (!$animal) {
            abort(404, 'Animal not found');
        }

        $trackingHistory = DB::table('tracking_data')
            ->where('device_id', $animal->device_id)
            ->orderBy('recorded_at', 'desc')
            ->limit(50)
            ->get();

        $latestPosition = DB::table('tracking_data')
            ->where('device_id', $animal->device_id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        return view('dashboard.animal-detail', compact('animal', 'trackingHistory', 'latestPosition'));
    }

    /**
     * Get latest tracking data
     */
    private function getLatestTracking(array $deviceIds)
    {
        if (empty($deviceIds)) {
            return collect();
        }

        // Check if cache table exists
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
            // Cache table doesn't exist
        }

        // Fallback: Query individual devices
        $tracking = collect();
        foreach ($deviceIds as $deviceId) {
            try {
                $latest = DB::table('tracking_data')
                    ->where('device_id', $deviceId)
                    ->orderBy('recorded_at', 'desc')
                    ->select('device_id', 'recorded_at as last_seen', 'latitude', 'longitude')
                    ->first();

                if ($latest) {
                    $tracking->put($deviceId, $latest);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $tracking;
    }
}
