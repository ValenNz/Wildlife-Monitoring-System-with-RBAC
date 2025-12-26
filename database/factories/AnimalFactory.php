<?php

namespace Database\Factories;

use App\Models\Animal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'species' => $this->faker->randomElement(['Orangutan', 'Harimau Sumatra', 'Badak Jawa', 'Cendrawasih']),
            'sex' => $this->faker->randomElement(['male', 'female']),
            'birth_date' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'tag_id' => 'TAG-' . Str::random(8),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
