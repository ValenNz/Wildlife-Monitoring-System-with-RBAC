<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Device;
use App\Models\EnvironmentalData;
use App\Models\TrackingData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BigDataSeeder extends Seeder
{
    public function run(): void
    {
        // Optimasi performa
        config(['app.debug' => false]);
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());

        $this->command->info('🚀 Memulai pengisian data besar...');

        // 1. Buat 2.000 satwa
        $this->command->info('1/5: Membuat 2.000 satwa...');
        Animal::factory(2000)->create();

        // 2. Buat 2.000 perangkat dan pasangkan ke satwa
        $this->command->info('2/5: Membuat 2.000 perangkat...');
        $animalIds = Animal::pluck('id')->toArray();
        $devices = [];
        for ($i = 0; $i < 2000; $i++) {
            $devices[] = [
                'device_id' => 'DEV-' . Str::random(10),
                'type' => 'gps',
                'status' => 'active',
                'battery_level' => rand(20, 100),
                'last_seen' => now(),
                'animal_id' => $animalIds[$i],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Device::insert($devices);
        $deviceIds = array_column($devices, 'device_id');

        // 3. Isi 1.000.000 tracking data
        $this->command->info('3/5: Mengisi 1.000.000 data tracking...');
        $this->insertTrackingData($deviceIds, 1_000_000);

        // 4. Isi 1.000.000 environmental data
        $this->command->info('4/5: Mengisi 1.000.000 data lingkungan...');
        $this->insertEnvironmentalData($deviceIds, 1_000_000);

        // 5. Data pendukung
        $this->command->info('5/5: Membuat data pendukung...');
        $this->createSupportData();

        $this->command->info('✅ Selesai! Total 2 juta+ baris data berhasil diisi.');
    }

    private function insertTrackingData(array $deviceIds, int $total): void
    {
        $chunkSize = 3000;
        $faker = \Faker\Factory::create('id_ID');

        for ($offset = 0; $offset < $total; $offset += $chunkSize) {
            $limit = min($chunkSize, $total - $offset);
            $chunk = [];

            for ($i = 0; $i < $limit; $i++) {
                $lat = $faker->latitude(-6.5, -0.1);  // Indonesia: Sumatra–Papua
                $lng = $faker->longitude(105, 116);

                $chunk[] = [
                    'device_id' => $deviceIds[array_rand($deviceIds)],
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'altitude' => rand(0, 3000),
                    'speed' => rand(0, 60),
                    'heading' => rand(0, 360),
                    'accuracy' => round(rand(100, 5000) / 100, 2),
                    'recorded_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'received_at' => now(),
                ];
            }

            TrackingData::insert($chunk);
            unset($chunk);

            if ($offset % (10 * $chunkSize) === 0) {
                gc_collect_cycles();
                $this->command->info("  → " . ($offset + $limit) . " / {$total} tracking data");
            }
        }
    }

    private function insertEnvironmentalData(array $deviceIds, int $total): void
    {
        $chunkSize = 4000;
        $faker = \Faker\Factory::create('id_ID');

        for ($offset = 0; $offset < $total; $offset += $chunkSize) {
            $limit = min($chunkSize, $total - $offset);
            $chunk = [];

            for ($i = 0; $i < $limit; $i++) {
                $chunk[] = [
                    'device_id' => $deviceIds[array_rand($deviceIds)],
                    'temperature' => rand(1800, 3500) / 100,   // 18.00–35.00
                    'humidity' => rand(6000, 9500) / 100,       // 60.00–95.00
                    'pressure' => rand(100000, 102000) / 100,   // 1000.00–1020.00
                    'light_level' => rand(0, 9999),             // ≤ 9999.99 → aman untuk DECIMAL(6,2)
                    'recorded_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'received_at' => now(),
                ];
            }

            EnvironmentalData::insert($chunk);
            unset($chunk);

            if ($offset % (10 * $chunkSize) === 0) {
                gc_collect_cycles();
                $this->command->info("  → " . ($offset + $limit) . " / {$total} environmental data");
            }
        }
    }

    private function createSupportData(): void
{
    \App\Models\User::factory()->create([
        'name' => 'Admin Dev',
        'email' => 'admin@wildlife.test',
    ]);

    // ✅ (latitude, longitude)
    // ✅ Orientasi CCW
    // ✅ SRID 4326 sebagai parameter kedua
    DB::statement("
    INSERT INTO risk_zones (name, zone_type, polygon, created_at, updated_at)
    VALUES (
        'Zona Perburuan Ilegal - Sumatra',
        'poaching',
        ST_GeomFromText('MULTIPOLYGON(((-0.5 101.5, 0.0 101.5, 0.0 102.0, -0.5 102.0, -0.5 101.5)))', 4326),
        NOW(),
        NOW()
    )
");
}
}
