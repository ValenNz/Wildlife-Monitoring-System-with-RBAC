<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_data', function (Blueprint $table) {
            $table->id();
            $table->string('device_id'); // mengacu ke devices.device_id (logical, bukan FK fisik)
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude', 7, 2)->nullable();
            $table->decimal('speed', 6, 2)->nullable(); // km/h
            $table->decimal('heading', 5, 2)->nullable(); // degrees
            $table->decimal('accuracy', 5, 2)->nullable(); // meters
            $table->timestamp('recorded_at'); // waktu data direkam di lapangan
            $table->timestamp('received_at')->useCurrent(); // waktu diterima server
        });

        // Index dasar untuk query performa
        Schema::table('tracking_data', function (Blueprint $table) {
            $table->index(['device_id', 'recorded_at']);
            $table->index(['latitude', 'longitude', 'recorded_at']);
            $table->index(['recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_data');
    }
};
