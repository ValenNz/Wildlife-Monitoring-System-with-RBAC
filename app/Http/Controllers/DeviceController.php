<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class DeviceController extends Controller
{
    /**
     * Display a listing of the devices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Validasi dan atur nilai per_page
        $perPage = $request->get('per_page', '10');
        $allowedPerPage = ['10', '100', '1000', 'all'];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = '10';
        }

        // 2. Bangun query dasar dengan JOIN ke animals
        $query = DB::table('devices as d')
            ->leftJoin('animals as a', 'd.animal_id', '=', 'a.id')
            ->select(
                'd.id',
                'd.device_id',
                'd.status',
                'd.battery_level',
                'd.last_seen',
                'd.installation_date',
                'a.name as animal_name',
                'a.species as species'
            );

        // 3. Terapkan filter pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('d.device_id', 'LIKE', "%{$request->search}%")
                  ->orWhere('a.name', 'LIKE', "%{$request->search}%")
                  ->orWhere('a.species', 'LIKE', "%{$request->search}%");
            });
        }

        // 4. Terapkan filter status
        if ($request->filled('status')) {
            $query->where('d.status', $request->status);
        }

        // 5. Ambil data berdasarkan pilihan per_page
        if ($perPage === 'all') {
            $devices = $query->get();
            $paginatedDevices = null;
            // Hitung total dari query asli sebelum get()
            $deviceCount = $query->count();
        } else {
            $perPage = (int)$perPage;
            $paginatedDevices = $query->paginate($perPage);
            $devices = $paginatedDevices->items();
            $deviceCount = $paginatedDevices->total();
        }

        // 6. Ambil statistik untuk card di atas
        $totalDevices = DB::table('devices')->count();
        $activeDevices = DB::table('devices')->where('status', 'active')->count();
        $inactiveDevices = DB::table('devices')->where('status', 'inactive')->count();
        $lowBatteryDevices = DB::table('devices')->where('battery_level', '<', 20)->count();

        // 7. Kirim data ke view
        return view('devices.index', compact(
            'devices',
            'paginatedDevices',
            'totalDevices',
            'activeDevices',
            'inactiveDevices',
            'lowBatteryDevices',
            'perPage',
            'deviceCount'
        ));
    }

    /**
     * Show the form for creating a new device.
     */
    public function create()
    {
        $animals = DB::table('animals')
            ->select('id', 'name', 'species')
            ->get();

        return view('devices.create', compact('animals'));
    }

    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string|unique:devices,device_id',
            'animal_id' => 'required|exists:animals,id',
            'status' => 'required|in:active,inactive',
            'battery_level' => 'required|integer|min:0|max:100',
            'installation_date' => 'required|date',
        ]);

        DB::table('devices')->insert([
            'device_id' => $request->device_id,
            'animal_id' => $request->animal_id,
            'status' => $request->status,
            'battery_level' => $request->battery_level,
            'installation_date' => $request->installation_date,
            'last_seen' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::route('devices.index')->with('success', 'Device created successfully!');
    }

    /**
     * Display the specified device.
     */
    public function show($id)
    {
        $device = DB::table('devices as d')
            ->leftJoin('animals as a', 'd.animal_id', '=', 'a.id')
            ->select(
                'd.*',
                'a.name as animal_name',
                'a.species as species_name'
            )
            ->where('d.id', $id)
            ->first();

        if (!$device) {
            return Redirect::route('devices.index')->with('error', 'Device not found');
        }

        return view('devices.show', compact('device'));
    }

    /**
     * Show the form for editing the specified device.
     */
    public function edit($id)
    {
        $device = DB::table('devices')->where('id', $id)->first();
        $animals = DB::table('animals')->select('id', 'name', 'species')->get();

        if (!$device) {
            return Redirect::route('devices.index')->with('error', 'Device not found');
        }

        return view('devices.edit', compact('device', 'animals'));
    }

    /**
     * Update the specified device in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'device_id' => 'required|string|unique:devices,device_id,' . $id . ',id',
            'animal_id' => 'required|exists:animals,id',
            'status' => 'required|in:active,inactive',
            'battery_level' => 'required|integer|min:0|max:100',
            'installation_date' => 'required|date',
        ]);

        DB::table('devices')
            ->where('id', $id)
            ->update([
                'device_id' => $request->device_id,
                'animal_id' => $request->animal_id,
                'status' => $request->status,
                'battery_level' => $request->battery_level,
                'installation_date' => $request->installation_date,
                'updated_at' => now(),
            ]);

        return Redirect::route('devices.index')->with('success', 'Device updated successfully!');
    }

    /**
     * Remove the specified device from storage.
     */
    public function destroy($id)
    {
        DB::table('devices')->where('id', $id)->delete();
        return Redirect::route('devices.index')->with('success', 'Device deleted successfully!');
    }

    /**
     * Export devices to CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('devices as d')
            ->leftJoin('animals as a', 'd.animal_id', '=', 'a.id')
            ->select(
                'd.device_id',
                'd.status',
                'd.battery_level',
                'd.last_seen',
                'a.name as animal_name',
                'a.species as species_name'
            );

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('d.device_id', 'LIKE', "%{$request->search}%")
                  ->orWhere('a.name', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('d.status', $request->status);
        }

        $devices = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="devices_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($devices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Device ID',
                'Animal Name',
                'Species',
                'Status',
                'Battery Level (%)',
                'Installation Date',
                'Last Seen'
            ]);

            foreach ($devices as $device) {
                fputcsv($file, [
                    $device->device_id,
                    $device->animal_name ?? 'Not assigned',
                    $device->species ?? 'N/A',
                    $device->status,
                    $device->battery_level,
                    $device->installation_date ? $device->installation_date->format('Y-m-d') : 'N/A',
                    $device->last_seen ? $device->last_seen->format('Y-m-d H:i:s') : 'Never'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Simulate a device test signal (FR-17: Notifikasi Teknis).
     */
    public function test($id, Request $request)
    {
        // Perbarui last_seen untuk simulasi sinyal diterima
        DB::table('devices')
            ->where('id', $id)
            ->update(['last_seen' => now(), 'updated_at' => now()]);

        // Simpan notifikasi ke tabel notifications (FR-17)
        DB::table('notifications')->insert([
            'title' => 'Device Test Successful',
            'message' => "Test signal sent to device ID {$id}.",
            'type' => 'info',
            'is_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Test signal sent.']);
    }

    /**
     * Reset device to factory settings (FR-08: Manajemen Perangkat).
     */
    public function reset($id, Request $request)
    {
        DB::table('devices')
            ->where('id', $id)
            ->update([
                'status' => 'active',
                'battery_level' => 100,
                'last_seen' => now(),
                'updated_at' => now(),
            ]);

        // Simpan notifikasi
        DB::table('notifications')->insert([
            'title' => 'Device Reset',
            'message' => "Device ID {$id} has been reset to factory settings.",
            'type' => 'warning',
            'is_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::back()->with('success', 'Device reset successfully!');
    }
}
