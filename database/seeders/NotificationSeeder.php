<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Mengisi 1 juta notification data dengan performa optimal
     */
    public function run(): void
    {
        // Optimasi performa
        config(['app.debug' => false]);

        $this->command->info('🔔 Memulai pengisian 1 juta notification data...');

        $total = 1_000_000;
        $chunkSize = 5000; // Insert 5000 records per batch
        $faker = Faker::create('id_ID');

        // Get user IDs for notifications (optional, bisa null jika tidak ada relasi)
        $userIds = DB::table('users')->pluck('id')->toArray();
        if (empty($userIds)) {
            $userIds = [null]; // Jika tidak ada user, set null
        }

        // Notification types based on schema
        $types = ['info', 'warning', 'error', 'debug'];

        // Notification templates untuk realistic data
        $templates = [
            'info' => [
                'Device Battery Low' => 'Device {device} battery level is at {level}%. Please recharge soon.',
                'Animal Movement Detected' => 'Animal {animal} has moved {distance}km from last position.',
                'Weather Update' => 'Weather conditions changed to {condition} in zone {zone}.',
                'System Maintenance' => 'System maintenance scheduled for {date}.',
                'Data Sync Completed' => 'Data synchronization completed successfully for {count} devices.',
                'New Device Added' => 'New tracking device {device} has been registered.',
                'Geofence Entry' => 'Animal {animal} entered protected zone {zone}.',
                'Geofence Exit' => 'Animal {animal} exited protected zone {zone}.',
            ],
            'warning' => [
                'Low Battery Warning' => 'Device {device} battery critical: {level}%! Immediate action required.',
                'Signal Weak' => 'Weak GPS signal detected for device {device} in area {area}.',
                'Connection Lost' => 'Lost connection with device {device} since {time}.',
                'Data Gap Detected' => 'No data received from {device} for {hours} hours.',
                'Unusual Activity' => 'Unusual movement pattern detected for animal {animal}.',
                'Zone Breach' => 'Animal {animal} approaching restricted zone {zone}.',
                'Device Malfunction' => 'Possible malfunction detected in device {device}.',
            ],
            'error' => [
                'Device Offline' => 'Device {device} is offline and unreachable.',
                'Data Transmission Failed' => 'Failed to transmit data from device {device}.',
                'GPS Error' => 'GPS module error detected in device {device}.',
                'Database Error' => 'Database connection error for device {device}.',
                'Sensor Failure' => 'Temperature sensor failure in device {device}.',
                'Critical Battery' => 'Device {device} battery depleted. Device shutdown imminent.',
                'System Error' => 'System error occurred while processing data from {device}.',
            ],
            'debug' => [
                'Debug Mode Enabled' => 'Debug mode enabled for device {device}.',
                'Test Notification' => 'This is a test notification for device {device}.',
                'Calibration Started' => 'Sensor calibration started for device {device}.',
                'Firmware Update' => 'Firmware update initiated for device {device}.',
                'Data Validation' => 'Data validation completed for {count} records.',
            ],
        ];

        $totalInserted = 0;

        for ($offset = 0; $offset < $total; $offset += $chunkSize) {
            $limit = min($chunkSize, $total - $offset);
            $chunk = [];

            for ($i = 0; $i < $limit; $i++) {
                // Random type
                $type = $types[array_rand($types)];

                // Random template for this type
                $templateKeys = array_keys($templates[$type]);
                $title = $templateKeys[array_rand($templateKeys)];
                $messageTemplate = $templates[$type][$title];

                // Generate dynamic message content
                $message = $this->generateMessage($messageTemplate, $faker);

                // Random user (optional)
                $userId = $userIds[array_rand($userIds)];

                // Random read status (70% unread, 30% read untuk realistic data)
                $isRead = $faker->boolean(30) ? 1 : 0;

                // Random trigger_type (optional field)
                $triggerTypes = ['device', 'system', 'user', 'scheduled', 'automatic'];
                $triggerType = $triggerTypes[array_rand($triggerTypes)];

                // Random created date (dalam 2 tahun terakhir)
                $createdAt = $faker->dateTimeBetween('-2 years', 'now');
                $updatedAt = $isRead ? $faker->dateTimeBetween($createdAt, 'now') : $createdAt;

                $chunk[] = [
                    'user_id' => $userId,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'is_read' => $isRead,
                    'trigger_id' => $faker->optional(0.7)->numberBetween(1, 2000),
                    'trigger_type' => $triggerType,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];
            }

            // Bulk insert
            DB::table('notifications')->insert($chunk);
            unset($chunk);

            $totalInserted += $limit;

            // Progress update setiap 50k records
            if ($totalInserted % 50000 === 0 || $totalInserted === $total) {
                gc_collect_cycles(); // Clean memory
                $percentage = round(($totalInserted / $total) * 100, 2);
                $this->command->info("  → {$totalInserted} / {$total} notifications ({$percentage}%)");
            }
        }

        $this->command->info('✅ Selesai! 1 juta notification data berhasil diisi.');

        // Show statistics
        $this->showStatistics();
    }

    /**
     * Generate dynamic message from template
     */
    private function generateMessage(string $template, $faker): string
    {
        $replacements = [
            '{device}' => 'DEV-' . strtoupper($faker->bothify('???###')),
            '{animal}' => $faker->firstName(),
            '{level}' => $faker->numberBetween(5, 20),
            '{distance}' => $faker->randomFloat(2, 0.5, 50),
            '{condition}' => $faker->randomElement(['Sunny', 'Rainy', 'Cloudy', 'Stormy']),
            '{zone}' => 'Zone-' . $faker->numberBetween(1, 20),
            '{date}' => $faker->date('Y-m-d H:i'),
            '{count}' => $faker->numberBetween(10, 500),
            '{area}' => 'Area-' . $faker->randomElement(['A', 'B', 'C', 'D']),
            '{time}' => $faker->time('H:i'),
            '{hours}' => $faker->numberBetween(2, 48),
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }

    /**
     * Show notification statistics
     */
    private function showStatistics(): void
    {
        $this->command->info("\n📊 Statistik Notification:");

        $total = DB::table('notifications')->count();
        $this->command->info("  Total: " . number_format($total));

        $unread = DB::table('notifications')->where('is_read', 0)->count();
        $this->command->info("  Unread: " . number_format($unread) . " (" . round(($unread/$total)*100, 2) . "%)");

        $read = DB::table('notifications')->where('is_read', 1)->count();
        $this->command->info("  Read: " . number_format($read) . " (" . round(($read/$total)*100, 2) . "%)");

        $this->command->info("\n  By Type:");
        $types = DB::table('notifications')
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        foreach ($types as $type) {
            $percentage = round(($type->count / $total) * 100, 2);
            $this->command->info("    - {$type->type}: " . number_format($type->count) . " ({$percentage}%)");
        }
    }
}
