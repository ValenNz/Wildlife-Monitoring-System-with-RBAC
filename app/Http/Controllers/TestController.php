<?php
// app/Http/Controllers/TestController.php
namespace App\Http\Controllers;

use App\Services\EnvironmentalCorrelationService;
use Illuminate\Http\Request;

class TestController extends Controller
{
   public function index()
    {
        // Ini hanya untuk testing, jangan gunakan di production
        $service = new EnvironmentalCorrelationService();
        $service->fetchAndStoreFromWeatherApi(-2.345678, 102.345678, now()->subDays(1));

        return response()->json(['message' => 'Weather data fetched and saved']);
    }
}
