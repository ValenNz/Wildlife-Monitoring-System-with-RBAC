<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemLogSeeder extends Seeder
{
    public function run()
    {
        $levels = ['info', 'warning', 'error', 'debug'];
        $logs = [];

        for ($i = 1; $i <= 1000000; $i++) {
            $context = json_encode([
                'user_id' => rand(1, 4),
                'action' => 'system_action_' . rand(1, 10),
                'ip' => '192.168.1.' . rand(1, 255)
            ]);

            $logs[] = [
                'level' => $levels[array_rand($levels)],
                'message' => 'System log message #' . $i,
                'context' => $context,
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now(),
            ];
        }

        DB::table('system_logs')->insert($logs);
    }
}
