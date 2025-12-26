<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel tanpa kolom polygon dulu
        Schema::create('risk_zones', function ($table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('zone_type', ['road', 'poaching', 'urban', 'protected', 'other']);
            $table->timestamps();
        });

        // Tambahkan kolom spasial MULTIPOLYGON dengan SRID 4326 via raw SQL
        DB::statement('ALTER TABLE risk_zones ADD COLUMN polygon MULTIPOLYGON NOT NULL SRID 4326');

        // Tambahkan spatial index
        DB::statement('ALTER TABLE risk_zones ADD SPATIAL INDEX spx_polygon (polygon)');
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_zones');
    }
};
