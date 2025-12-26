<?php

namespace Database\Factories;

use App\Models\EnvironmentalData;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnvironmentalDataFactory extends Factory
{
    protected $model = EnvironmentalData::class;

    public function definition(): array
    {
        return [
            'device_id' => '', // diisi dinamis
            'temperature' => $this->faker->numberBetween(18, 35),
            'humidity' => $this->faker->numberBetween(60, 95),
            'pressure' => $this->faker->numberBetween(1000, 1020),
            'light_level' => $this->faker->numberBetween(0, 100000),
            'recorded_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'received_at' => now(),
        ];
    }
}
