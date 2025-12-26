<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskZoneSeeder extends Seeder
{
    public function run()
    {
        $zoneTypes = ['road', 'poaching', 'urban', 'protected', 'other'];

        for ($i = 1; $i <= 50; $i++) {
            // Generate koordinat pusat random
            $centerLat = -2.5 + (mt_rand(-100000, 100000) / 100000) * 1.5;
            $centerLon = 102.0 + (mt_rand(-100000, 100000) / 100000) * 1.5;
            $size = 0.01 + (mt_rand(0, 50000) / 100000) * 0.04;

            // Buat koordinat polygon (persegi sederhana)
            $coords = [
                [$centerLat, $centerLon],
                [$centerLat + $size, $centerLon],
                [$centerLat + $size, $centerLon + $size],
                [$centerLat, $centerLon + $size],
                [$centerLat, $centerLon] // Tutup polygon
            ];

            // Format koordinat untuk WKT sebagai MULTIPOLYGON
$wktCoords = implode(', ', array_map(function($coord) {
    return $coord[0] . ' ' . $coord[1];
}, $coords));

// ✅ Bangun WKT sebagai MULTIPOLYGON, bukan POLYGON
$wkt = "MULTIPOLYGON((($wktCoords)))"; // Perhatikan ((()))

DB::table('risk_zones')->insert([
    'name' => 'Zone ' . $i,
    'description' => 'Description for zone ' . $i,
    'zone_type' => $zoneTypes[array_rand($zoneTypes)],
    'polygon' => DB::raw("ST_GeomFromText('$wkt', 4326)"), // Tidak pakai ST_Multi
    'created_at' => now(),
    'updated_at' => now(),
]);
        }
    }
}
