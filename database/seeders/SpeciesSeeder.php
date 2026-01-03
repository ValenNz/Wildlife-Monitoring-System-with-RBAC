<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpeciesSeeder extends Seeder
{
    public function run()
    {
        DB::table('species')->insert([
            [
                'common_name' => 'Orangutan Sumatra',
                'scientific_name' => 'Pongo abelii',
                'conservation_status' => 'Critically Endangered',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'common_name' => 'Harimau Sumatra',
                'scientific_name' => 'Panthera tigris sumatrae',
                'conservation_status' => 'Critically Endangered',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'common_name' => 'Badak Sumatra',
                'scientific_name' => 'Dicerorhinus sumatrensis',
                'conservation_status' => 'Critically Endangered',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
