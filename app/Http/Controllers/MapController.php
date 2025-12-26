<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MapController extends Controller
{
    /**
     * Display live map with all tracked animals
     */
    public function index(Request $request)
{
    $animalId = $request->get('animal');

    $query = DB::table('animals as a')
        ->leftJoin('devices as d', 'a.id', '=', 'd.animal_id')
        ->select(
            'a.id',
            'a.name',
            'a.species',
            'a.tag_id',
            'd.id as device_pk',
            'd.device_id',
            'd.status',
            'd.battery_level',
            'd.last_seen'
        );

    if ($animalId) {
        $query->where('a.id', $animalId);
    }

    $animals = $query->get();

    $deviceIds = $animals->pluck('device_id')->filter()->toArray();
    $positions = $this->getLatestPositions($deviceIds);

    $animalsWithPositions = $animals->map(function($animal) use ($positions) {
        $position = $positions->get($animal->device_id);

        $animal->latitude = $position->latitude ?? null;
        $animal->longitude = $position->longitude ?? null;
        $animal->altitude = $position->altitude ?? null;
        $animal->speed = $position->speed ?? null;
        $animal->heading = $position->heading ?? null;
        $animal->recorded_at = $position->recorded_at ?? null;

        return $animal;
    });

    $totalAnimals = $animals->count();
    $activeAnimals = $animals->where('status', 'active')->count();
    $onlineDevices = $animals->where('status', 'active')->count();

    // Get geozones dengan konversi polygon
    $geozones = DB::table('risk_zones')
        ->select(
            'id',
            'name',
            'zone_type',
            'description',
            DB::raw("ST_AsText(polygon) as polygon_wkt")
        )
        ->get()
        ->map(function($zone) {
            try {
                // Konversi WKT ke array koordinat
                $coords = $this->wktToCoordinates($zone->polygon_wkt);
                $zone->polygon = $coords;
                return $zone;
            } catch (\Exception $e) {
                return null;
            }
        })
        ->filter();

    return view('map.index', compact(
        'animalsWithPositions',
        'totalAnimals',
        'activeAnimals',
        'onlineDevices',
        'geozones',
        'animalId'
    ));
}

    /**
     * Get real-time positions (API endpoint)
     */
    public function getPositions(Request $request)
    {
        $animalId = $request->get('animal_id');

        $query = DB::table('animals as a')
            ->leftJoin('devices as d', 'a.id', '=', 'd.animal_id')
            ->select('a.id', 'a.name', 'a.species', 'd.device_id', 'd.status');

        if ($animalId) {
            $query->where('a.id', $animalId);
        }

        $animals = $query->get();
        $deviceIds = $animals->pluck('device_id')->filter()->toArray();
        $positions = $this->getLatestPositions($deviceIds);

        $result = $animals->map(function($animal) use ($positions) {
            $position = $positions->get($animal->device_id);

            return [
                'id' => $animal->id,
                'name' => $animal->name,
                'species' => $animal->species,
                'status' => $animal->status,
                'latitude' => $position->latitude ?? null,
                'longitude' => $position->longitude ?? null,
                'altitude' => $position->altitude ?? null,
                'speed' => $position->speed ?? null,
                'heading' => $position->heading ?? null,
                'recorded_at' => $position->recorded_at ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'timestamp' => Carbon::now()->toDateTimeString(),
            'data' => $result
        ]);
    }

    /**
     * Get heatmap data
     */
    public function getHeatmap(Request $request)
{
    $days = $request->get('days', 7);

    $heatmapData = DB::table('tracking_data as t')
        ->join('devices as d', 't.device_id', '=', 'd.device_id')
        ->join('animals as a', 'd.animal_id', '=', 'a.id')
        ->where('t.recorded_at', '>=', Carbon::now()->subDays($days))
        ->select(
            't.latitude',
            't.longitude',
            DB::raw('COUNT(*) as intensity')
        )
        ->groupBy('t.latitude', 't.longitude')
        ->havingRaw('COUNT(*) > 1')
        ->limit(1000)
        ->get();

    return response()->json([
        'success' => true,
        'days' => $days,
        'points' => $heatmapData->count(),
        'data' => $heatmapData
    ]);
}

    /**
     * Track specific animal with path history
     */
    public function trackAnimal($id, Request $request)
    {
        $hours = $request->get('hours', 24);

        $animal = DB::table('animals as a')
            ->leftJoin('devices as d', 'a.id', '=', 'd.animal_id')
            ->where('a.id', $id)
            ->select('a.*', 'd.id as device_pk', 'd.device_id', 'd.status',
                     'd.battery_level', 'd.last_seen')
            ->first();

        if (!$animal) {
            abort(404, 'Animal not found');
        }

        $trackingPath = DB::table('tracking_data')
            ->where('device_id', $animal->device_id)
            ->where('recorded_at', '>=', Carbon::now()->subHours($hours))
            ->orderBy('recorded_at', 'asc')
            ->select('latitude', 'longitude', 'altitude', 'speed', 'recorded_at')
            ->get();

        $latestPosition = DB::table('tracking_data')
            ->where('device_id', $animal->device_id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        return view('map.track-animal', compact(
            'animal',
            'trackingPath',
            'latestPosition',
            'hours'
        ));
    }

    /**
     * Get geozones (risk_zones)
     */
public function getGeozones()
{
    $geozones = DB::table('risk_zones')
        ->select('id', 'name', 'zone_type', 'description',
                 DB::raw("ST_AsGeoJSON(polygon) as polygon"))
        ->get()
        ->map(function($zone) {
            try {
                $geojson = json_decode($zone->polygon, true);
                // Ambil koordinat pertama dari multipolygon
                if (isset($geojson['coordinates'][0][0])) {
                    $zone->polygon = $geojson['coordinates'][0][0];
                } else {
                    $zone->polygon = null;
                }
            } catch (\Exception $e) {
                $zone->polygon = null;
            }
            return $zone;
        })
        ->filter(fn($zone) => $zone->polygon !== null);

    return response()->json([
        'success' => true,
        'total' => $geozones->count(),
        'data' => $geozones
    ]);
}

    /**
     * Get animals in specific geozone
     */
    public function getAnimalsInZone($zoneId)
    {
        $zone = DB::table('risk_zones')
            ->where('id', $zoneId)
            ->first();

        if (!$zone) {
            return response()->json(['success' => false, 'message' => 'Zone not found'], 404);
        }

        $animals = DB::table('animals as a')
            ->leftJoin('devices as d', 'a.id', '=', 'd.animal_id')
            ->select('a.id', 'a.name', 'a.species', 'd.device_id', 'd.status')
            ->get();

        $deviceIds = $animals->pluck('device_id')->filter()->toArray();
        $positions = $this->getLatestPositions($deviceIds);

        // Filter animals inside zone (simplified)
        $animalsInZone = $animals->filter(function($animal) use ($positions) {
            return $positions->get($animal->device_id) !== null;
        });

        return response()->json([
            'success' => true,
            'zone' => $zone->name,
            'total' => $animalsInZone->count(),
            'data' => $animalsInZone->values()
        ]);
    }

    /**
     * Get latest positions for devices
     */
    private function getLatestPositions(array $deviceIds)
    {
        if (empty($deviceIds)) {
            return collect();
        }

        try {
            $positions = DB::table('latest_tracking_cache')
                ->whereIn('device_id', $deviceIds)
                ->select('device_id', 'last_seen as recorded_at', 'latitude', 'longitude',
                         'altitude', 'speed', 'heading')
                ->get()
                ->keyBy('device_id');

            if ($positions->isNotEmpty()) {
                return $positions;
            }
        } catch (\Exception $e) {
            // Cache table doesn't exist
        }

        // Fallback
        $positions = collect();
        foreach ($deviceIds as $deviceId) {
            try {
                $latest = DB::table('tracking_data')
                    ->where('device_id', $deviceId)
                    ->orderBy('recorded_at', 'desc')
                    ->first();

                if ($latest) {
                    $positions->put($deviceId, $latest);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $positions;
    }

    private function wktToCoordinates($wkt)
    {
        // Hanya handle POLYGON dan MULTIPOLYGON sederhana
        if (strpos($wkt, 'POLYGON') !== false) {
            // POLYGON((lat1 lon1, lat2 lon2, ...))
            preg_match('/POLYGON\(\((.*?)\)\)/', $wkt, $matches);
            if (!isset($matches[1])) return [];

            $points = explode(',', $matches[1]);
            return array_map(function($point) {
                $coords = explode(' ', trim($point));
                return [(float)$coords[1], (float)$coords[0]]; // [lat, lon]
            }, $points);
        }

        return [];
    }

}
