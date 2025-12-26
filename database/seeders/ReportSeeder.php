<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    public function run()
    {
        // ✅ Pastikan ada minimal 4 user
        $users = [
            ['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')],
            ['name' => 'User1', 'email' => 'user1@example.com', 'password' => bcrypt('password')],
            ['name' => 'User2', 'email' => 'user2@example.com', 'password' => bcrypt('password')],
            ['name' => 'User3', 'email' => 'user3@example.com', 'password' => bcrypt('password')],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }

        // Setelah user ada, baru buat reports
        $reportTypes = ['activity', 'device', 'environmental', 'incident'];
        $reports = [];

        for ($i = 1; $i <= 100; $i++) {
            $content = json_encode([
                'sample_data' => 'This is sample report data',
                'generated_at' => now()->toDateTimeString()
            ]);

            $reports[] = [
                'title' => 'Report ' . $i,
                'report_type' => $reportTypes[array_rand($reportTypes)],
                'generated_by' => rand(1, 4), // Sekarang aman karena user 1-4 sudah ada
                'generated_at' => now()->subDays(rand(0, 30)),
                'period_start' => now()->subDays(rand(7, 30)),
                'period_end' => now()->subDays(rand(0, 7)),
                'content' => $content,
                'metadata' => json_encode(['version' => '1.0']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('reports')->insert($reports);
    }
}
