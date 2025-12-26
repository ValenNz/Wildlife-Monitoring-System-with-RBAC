<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hanya jalankan jika env = local/testing
        if (app()->environment('local', 'testing')) {
            $this->call([
                UserSeeder::class,           // 👈 Pastikan user ada dulu
                RiskZoneSeeder::class,       // 👈 Zona risiko
                ReportSeeder::class,         // 👈 Laporan
                IncidentSeeder::class,       // 👈 Insiden
                NotificationSeeder::class,   // 👈 Notifikasi
                SystemLogSeeder::class,      // 👈 Log sistem
                BigDataSeeder::class,        // 👈 Data besar (jika perlu)
            ]);
        }
    }
}
