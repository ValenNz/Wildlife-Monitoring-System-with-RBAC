<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('animal_risk_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('animal_id');
            $table->unsignedBigInteger('risk_zone_id');
            $table->timestamp('entered_at'); // Waktu masuk zona
            $table->timestamp('exited_at')->nullable(); // Waktu keluar zona (opsional)
            $table->timestamps();

            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
            $table->foreign('risk_zone_id')->references('id')->on('risk_zones')->onDelete('cascade');

            // Unique constraint untuk mencegah duplikasi
            $table->unique(['animal_id', 'risk_zone_id', 'entered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_risk_zones');
    }
};
