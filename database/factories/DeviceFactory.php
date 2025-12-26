<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'device_id' => 'DEV-' . $this->faker->unique()->bothify('????-####'),
            'type' => 'gps',
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'maintenance']), // 75% active
            'battery_level' => $this->faker->numberBetween(20, 100),
            'last_seen' => now(),
            'animal_id' => null, // akan diisi setelah animal dibuat
        ];
    }
}
