<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnimalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = in_array($request->get('sort'), ['name', 'species', 'gender'])
            ? $request->get('sort') : 'name';
        $sortOrder = in_array($request->get('order'), ['asc', 'desc'])
            ? $request->get('order') : 'asc';

        $query = DB::table('animals as a')
            ->join('species as s', 'a.species_id', '=', 's.species_id')
            ->leftJoin('tracking_devices as d', 'a.animal_id', '=', 'd.animal_id')
            ->select(
                'a.animal_id', 'a.name', 's.common_name as species',
                'a.gender', 'a.is_active', 'd.is_active as device_status'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('a.name', 'LIKE', "%{$search}%")
                  ->orWhere('s.common_name', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy($sortBy, $sortOrder);

        if ($request->has('paginated') && $request->paginated == 1) {
            $animals = $query->paginate(10)->appends($request->query());
        } else {
            $animals = $query->get();
        }

        return view('animals.index', compact('animals', 'search', 'sortBy', 'sortOrder'));
    }

    public function show($id)
    {
        $animal = DB::table('animals as a')
            ->join('species as s', 'a.species_id', '=', 's.species_id')
            ->leftJoin('tracking_devices as d', 'a.animal_id', '=', 'd.animal_id')
            ->where('a.animal_id', $id)
            ->select(
                'a.*', 's.common_name as species_name',
                'd.device_id', 'd.model as device_model', 'd.is_active as device_is_active'
            )
            ->firstOrFail();

        $trackingHistory = DB::table('gps_readings')
            ->where('device_id', $animal->device_id)
            ->orderBy('recorded_at', 'desc')
            ->limit(50)
            ->get();

        $latestPosition = DB::table('gps_readings')
            ->where('device_id', $animal->device_id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        return view('animals.show', compact('animal', 'trackingHistory', 'latestPosition'));
    }
}
