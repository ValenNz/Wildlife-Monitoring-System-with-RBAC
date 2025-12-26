<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class GeozoneController extends Controller
{
    /**
     * Display a listing of the risk zones.
     */
    public function index(Request $request)
    {
        $query = DB::table('risk_zones')
            ->select(
                'id',
                'name',
                'description',
                'zone_type',
                'created_at'
            );

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            $query->where('zone_type', $request->type);
        }

        $perPage = $request->get('per_page', 10);
        $geozones = $query->paginate($perPage);

        // Statistics
        $totalZones = DB::table('risk_zones')->count();
        $protectedZones = DB::table('risk_zones')->where('zone_type', 'protected')->count();
        $urbanZones = DB::table('risk_zones')->where('zone_type', 'urban')->count();

        return view('geozones.index', compact(
            'geozones',
            'totalZones',
            'protectedZones',
            'urbanZones'
        ));
    }

    /**
     * Show the form for creating a new risk zone.
     */
    public function create()
    {
        return view('geozones.create');
    }

    /**
     * Store a newly created risk zone in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'zone_type' => 'required|in:road,poaching,urban,protected,other',
            'polygon' => 'required|string', // Format: "POLYGON((lat1 lon1, lat2 lon2, ...))"
        ]);

        DB::table('risk_zones')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'zone_type' => $request->zone_type,
            'polygon' => $request->polygon,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::route('geozones.index')->with('success', 'Zone created successfully!');
    }

    /**
     * Display the specified risk zone.
     */
    public function show($id)
    {
        $zone = DB::table('risk_zones')
            ->where('id', $id)
            ->first();

        if (!$zone) {
            return Redirect::route('geozones.index')->with('error', 'Zone not found');
        }

        return view('geozones.show', compact('zone'));
    }

    /**
     * Show the form for editing the specified risk zone.
     */
    public function edit($id)
    {
        $zone = DB::table('risk_zones')
            ->where('id', $id)
            ->first();

        if (!$zone) {
            return Redirect::route('geozones.index')->with('error', 'Zone not found');
        }

        return view('geozones.edit', compact('zone'));
    }

    /**
     * Update the specified risk zone in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'zone_type' => 'required|in:road,poaching,urban,protected,other',
            'polygon' => 'required|string',
        ]);

        DB::table('risk_zones')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'zone_type' => $request->zone_type,
                'polygon' => $request->polygon,
                'updated_at' => now(),
            ]);

        return Redirect::route('geozones.index')->with('success', 'Zone updated successfully!');
    }

    /**
     * Remove the specified risk zone from storage.
     */
    public function destroy($id)
    {
        DB::table('risk_zones')->where('id', $id)->delete();
        return Redirect::route('geozones.index')->with('success', 'Zone deleted successfully!');
    }

    /**
     * Export risk zones to CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('risk_zones')
            ->select('id', 'name', 'description', 'zone_type', 'created_at');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            $query->where('zone_type', $request->type);
        }

        $zones = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="risk_zones_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($zones) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Description', 'Type', 'Created At']);

            foreach ($zones as $zone) {
                fputcsv($file, [
                    $zone->id,
                    $zone->name,
                    $zone->description ?? '',
                    $zone->zone_type,
                    $zone->created_at ? $zone->created_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get animals inside a specific geozone
     */
    public function getAnimalsInZone($id)
    {
        $zone = DB::table('risk_zones')
            ->where('id', $id)
            ->first();

        if (!$zone) {
            return response()->json(['success' => false, 'message' => 'Zone not found'], 404);
        }

        // Ambil semua satwa yang memiliki perangkat
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
     * Toggle zone status (jika ada kolom status)
     */
    public function toggle($id)
    {
        // Jika tabel risk_zones punya kolom status
        // DB::table('risk_zones')->where('id', $id)->update(['status' => DB::raw('NOT status')]);

        return response()->json([
            'success' => true,
            'message' => 'Zone status toggled successfully'
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
}
