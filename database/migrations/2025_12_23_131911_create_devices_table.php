<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // ID fisik dari perangkat
            $table->string('type')->default('gps');
            $table->enum('status', ['active', 'inactive', 'lost', 'maintenance'])->default('active');
            $table->decimal('battery_level', 5, 2)->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->foreignId('animal_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
