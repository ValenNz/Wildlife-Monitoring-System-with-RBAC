<?php

namespace App\Services;
use Illuminate\Http\Client\Response;

use App\Models\TrackingData;
use App\Models\EnvironmentalData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EnvironmentalCorrelationService
{
    // Spatial tolerance: ~1 km in degrees
    public const SPATIAL_TOLERANCE = 0.009;

    // Temporal tolerance: ±15 minutes
    public const TEMPORAL_TOLERANCE_MINUTES = 15;

    /**
     * Mengorelasikan data pelacakan satwa dengan data cuaca berdasarkan lokasi dan waktu.
     *
     * @param int $animalId
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @return Collection
     * /**
 * (Optional) Fetch weather data from WeatherAPI and save to database.
 *
 * @param float $latitude
 * @param float $longitude
 * @param \DateTimeInterface $date
 * @return void
 * @throws \Exception
 */

    public function correlateByAnimalId(int $animalId, string $startDate, string $endDate): Collection
    {
        // Fetch all tracking data for specific animal within period
        $trackingRecords = TrackingData::with('device.animal')
            ->whereHas('device.animal', fn($query) => $query->where('id', $animalId))
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->get();

        $result = collect();

        foreach ($trackingRecords as $tracking) {
            // Calculate time range: ±15 minutes
            $startTime = $tracking->recorded_at->copy()->subMinutes(self::TEMPORAL_TOLERANCE_MINUTES);
            $endTime = $tracking->recorded_at->copy()->addMinutes(self::TEMPORAL_TOLERANCE_MINUTES);

            // Find nearest environmental data (spatial + temporal)
            $envData = EnvironmentalData::whereBetween('recorded_at', [$startTime, $endTime])
                ->whereRaw("ABS(`latitude` - ?) <= ?", [$tracking->latitude, self::SPATIAL_TOLERANCE])
                ->whereRaw("ABS(`longitude` - ?) <= ?", [$tracking->longitude, self::SPATIAL_TOLERANCE])
                ->orderByRaw("ABS(TIMESTAMPDIFF(SECOND, `recorded_at`, ?))", [$tracking->recorded_at])
                ->first();

            $result->push([
                'tracking_data' => $tracking,
                'environmental_data' => $envData,
            ]);
        }

        return $result;
    }

/**
 * (Optional) Fetch weather data from WeatherAPI and save to database.
 * Corresponds to FR-13 and Use Case #13 in SRS & DPPL documents.
 */
/**
 * (Optional) Fetch weather data from WeatherAPI and save to database.
 * Corresponds to FR-13 and Use Case #13 in SRS & DPPL documents.
 */
/**
 * (Optional) Fetch weather data from WeatherAPI and save to database.
 * Corresponds to FR-13 and Use Case #13 in SRS & DPPL documents.
 */
public function fetchAndStoreFromWeatherApi(float $latitude, float $longitude, \DateTimeInterface $date): void
{
    // Ensure API key is configured in .env
    $apiKey = config('services.weatherapi.key');
    if (!$apiKey) {
        Log::warning('WeatherAPI key not configured.');
        return;
    }

    $formattedDate = $date->format('Y-m-d');
    $url = "https://api.weatherapi.com/v1/history.json?key={$apiKey}&q={$latitude},{$longitude}&dt={$formattedDate}";

    try {
        // Gunakan Http::get() dan simpan respon dalam variabel dengan type hint
        $response = Http::timeout(10)->get($url);

        // Periksa status kode respons (200 OK)
        if ($response instanceof Response && $response->getStatusCode() === 200) {
            $data = $response->json(); // Ambil body sebagai array

            // Pastikan struktur data valid
            $forecastDay = $data['forecast']['forecastday'][0]['day'] ?? null;

            if ($forecastDay) {
                EnvironmentalData::updateOrCreate(
                    [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'recorded_at' => $date,
                    ],
                    [
                        'temperature' => $forecastDay['avgtemp_c'] ?? null,
                        'humidity' => $forecastDay['avghumidity'] ?? null,
                        'precipitation' => $forecastDay['totalprecip_mm'] ?? null,
                        'pressure' => null, // WeatherAPI tidak menyediakan tekanan di data historis gratis
                    ]
                );
            }
        } else {
            Log::error('Failed to fetch weather data', [
                'url' => $url,
                'status_code' => $response instanceof Response ? $response->getStatusCode() : 'Unknown',
                'body' => $response instanceof Response ? $response->body() : 'No response',
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Exception while fetching weather data', [
            'url' => $url,
            'error_message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString()
        ]);
    }
}
}
