<?php

namespace Database\Factories;

use App\Models\TrackingData;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingDataFactory extends Factory
{
    protected $model = TrackingData::class;

    public function definition(): array
    {
        // Lokasi realistis di Indonesia
        $lat = $this->faker->latitude(-6.5, -0.1);  // Sumatra–Papua
        $lng = $this->faker->longitude(105, 116);

        return [
            'device_id' => '', // akan diisi dinamis
            'latitude' => $lat,
            'longitude' => $lng,
            'altitude' => $this->faker->numberBetween(0, 3000),
            'speed' => $this->faker->numberBetween(0, 60), // km/h
            'heading' => $this->faker->numberBetween(0, 360),
            'accuracy' => $this->faker->randomFloat(2, 1, 50),
            'recorded_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'received_at' => now(),
        ];
    }
}
