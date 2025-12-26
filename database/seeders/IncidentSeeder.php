<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seed incidents table with configurable amount of data
     */
    public function run(): void
    {
        $this->command->info('🚨 Memulai pengisian incident data...');

        // Configuration
        $total = $this->command->ask('How many incidents to create?', 10000);
        $chunkSize = 2000;

        $faker = Faker::create('id_ID');

        // Get IDs for foreign keys
        $animalIds = DB::table('animals')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($animalIds)) {
            $animalIds = [null];
        }
        if (empty($userIds)) {
            $userIds = [null];
        }

        // Incident templates
        $templates = [
            'critical' => [
                'Poaching Activity Detected' => 'Suspicious movement and vehicles detected in restricted area {zone}. Immediate intervention required.',
                'Animal in Danger Zone' => 'Animal {animal} entered high-risk poaching zone {zone}. Critical response needed.',
                'Device Completely Offline' => 'No signal from device {device} for over 24 hours. Animal safety at risk.',
                'Severe Habitat Destruction' => 'Major habitat destruction detected in {zone}. Multiple animals affected.',
                'Illegal Logging Activity' => 'Heavy machinery and logging activity in protected zone {zone}.',
            ],
            'high' => [
                'Device Battery Critical' => 'Device {device} battery at {level}%. Replacement urgent.',
                'Animal Outside Safe Zone' => 'Animal {animal} left protected area. Approaching danger zone {zone}.',
                'Unusual Behavior Detected' => 'Animal {animal} showing unusual movement patterns. Possible injury or distress.',
                'Zone Breach Alert' => 'Unauthorized entry detected in restricted zone {zone}.',
                'Disease Symptoms Observed' => 'Animal {animal} showing signs of illness. Veterinary attention needed.',
            ],
            'medium' => [
                'Device Signal Weak' => 'Weak GPS signal from device {device}. Signal strength at {level}%.',
                'Weather Alert' => 'Severe weather conditions in zone {zone}. Monitoring affected.',
                'Animal Near Road' => 'Animal {animal} approaching roadway. Traffic alert issued.',
                'Habitat Disturbance' => 'Human activity detected near habitat zone {zone}.',
                'Equipment Maintenance Due' => 'Device {device} requires scheduled maintenance.',
            ],
            'low' => [
                'Routine Check Required' => 'Device {device} due for routine inspection.',
                'Data Gap Detected' => 'Minor data transmission gap for device {device}.',
                'Animal Resting' => 'Animal {animal} in rest mode. No activity for {hours} hours.',
                'System Test' => 'Scheduled system test for zone {zone}.',
                'Update Available' => 'Firmware update available for device {device}.',
            ],
        ];

        $severities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['open', 'investigating', 'resolved', 'closed'];
        $statusWeights = [40, 30, 20, 10]; // Weight distribution

        $totalInserted = 0;

        for ($offset = 0; $offset < $total; $offset += $chunkSize) {
            $limit = min($chunkSize, $total - $offset);
            $chunk = [];

            for ($i = 0; $i < $limit; $i++) {
                // Random severity (more low/medium, less critical)
                $severityRand = rand(1, 100);
                if ($severityRand <= 40) {
                    $severity = 'low';
                } elseif ($severityRand <= 70) {
                    $severity = 'medium';
                } elseif ($severityRand <= 90) {
                    $severity = 'high';
                } else {
                    $severity = 'critical';
                }

                // Random status (weighted)
                $statusRand = rand(1, 100);
                if ($statusRand <= 40) {
                    $status = 'open';
                } elseif ($statusRand <= 70) {
                    $status = 'investigating';
                } elseif ($statusRand <= 90) {
                    $status = 'resolved';
                } else {
                    $status = 'closed';
                }

                // Get template
                $templateKeys = array_keys($templates[$severity]);
                $title = $templateKeys[array_rand($templateKeys)];
                $descriptionTemplate = $templates[$severity][$title];

                // Generate description
                $description = str_replace(
                    ['{animal}', '{device}', '{zone}', '{level}', '{hours}'],
                    [
                        $faker->firstName(),
                        'DEV-' . strtoupper($faker->bothify('???###')),
                        'Zone-' . $faker->randomElement(['A', 'B', 'C', 'D', 'E']),
                        $faker->numberBetween(5, 20),
                        $faker->numberBetween(2, 48),
                    ],
                    $descriptionTemplate
                );

                // Random dates
                $reportedAt = $faker->dateTimeBetween('-6 months', 'now');
                $resolvedAt = null;
                $resolvedBy = null;
                $resolutionNotes = null;

                if ($status === 'resolved' || $status === 'closed') {
                    $resolvedAt = $faker->dateTimeBetween($reportedAt, 'now');
                    $resolvedBy = $userIds[array_rand($userIds)];
                    $resolutionNotes = $faker->sentence(15);
                }

                // Random location (Indonesia coordinates)
                $latitude = $faker->latitude(-6.5, -0.1);
                $longitude = $faker->longitude(105, 116);

                $chunk[] = [
                    'user_id' => $userIds[array_rand($userIds)],
                    'title' => $title,
                    'description' => $description,
                    'severity' => $severity,
                    'status' => $status,
                    'animal_id' => $faker->optional(0.7)->randomElement($animalIds),
                    'assigned_to' => $faker->optional(0.6)->randomElement($userIds),
                    'location' => 'Zone-' . $faker->randomElement(['A', 'B', 'C', 'D', 'E']) . ' Sector-' . $faker->numberBetween(1, 10),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'reported_at' => $reportedAt,
                    'resolved_at' => $resolvedAt,
                    'resolved_by' => $resolvedBy,
                    'resolution_notes' => $resolutionNotes,
                    'created_at' => $reportedAt,
                    'updated_at' => $resolvedAt ?? $reportedAt,
                ];
            }

            DB::table('incidents')->insert($chunk);
            unset($chunk);

            $totalInserted += $limit;

            if ($totalInserted % 10000 === 0 || $totalInserted === $total) {
                gc_collect_cycles();
                $percentage = round(($totalInserted / $total) * 100, 2);
                $this->command->info("  → {$totalInserted} / {$total} incidents ({$percentage}%)");
            }
        }

        $this->command->info('✅ Selesai! ' . number_format($totalInserted) . ' incident data berhasil diisi.');

        // Show statistics
        $this->showStatistics();
    }

    /**
     * Show incident statistics
     */
    private function showStatistics(): void
    {
        $this->command->info("\n📊 Statistik Incidents:");

        $total = DB::table('incidents')->count();
        $this->command->info("  Total: " . number_format($total));

        $this->command->info("\n  By Severity:");
        $severities = DB::table('incidents')
            ->select('severity', DB::raw('COUNT(*) as count'))
            ->groupBy('severity')
            ->get();

        foreach ($severities as $sev) {
            $percentage = round(($sev->count / $total) * 100, 2);
            $this->command->info("    - {$sev->severity}: " . number_format($sev->count) . " ({$percentage}%)");
        }

        $this->command->info("\n  By Status:");
        $statuses = DB::table('incidents')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($statuses as $stat) {
            $percentage = round(($stat->count / $total) * 100, 2);
            $this->command->info("    - {$stat->status}: " . number_format($stat->count) . " ({$percentage}%)");
        }
    }
}
