<?php
// app/Http/Controllers/EnvironmentalDataController.php

namespace App\Http\Controllers;

use App\Services\EnvironmentalCorrelationService;
use Illuminate\Http\Request;

class EnvironmentalDataController extends Controller
{
    public function correlate(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $service = new EnvironmentalCorrelationService();
        $data = $service->correlateByAnimalId(
            $request->animal_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json($data);
    }
}
