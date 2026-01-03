<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    // Schema::table('risk_zones', function (Blueprint $table) {
    //     $table->index('zone_type');
    //     $table->index('is_active');
    // });

    // Schema::table('environmental_data', function (Blueprint $table) {
    //     $table->index(['latitude', 'longitude']);
    //     $table->index('recorded_at');
    // });

    Schema::table('incidents', function (Blueprint $table) {
        $table->index('risk_zone_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
