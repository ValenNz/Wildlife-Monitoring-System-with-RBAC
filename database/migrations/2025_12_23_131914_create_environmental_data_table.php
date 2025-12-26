<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('environmental_data', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->decimal('temperature', 5, 2)->nullable(); // °C
            $table->decimal('humidity', 5, 2)->nullable();     // %
            $table->decimal('pressure', 7, 2)->nullable();     // hPa
            $table->decimal('light_level', 6, 2)->nullable();  // lux
            $table->timestamp('recorded_at');
            $table->timestamp('received_at')->useCurrent();
        });

        Schema::table('environmental_data', function (Blueprint $table) {
            $table->index(['device_id', 'recorded_at']);
            $table->index(['recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('environmental_data');
    }
};
