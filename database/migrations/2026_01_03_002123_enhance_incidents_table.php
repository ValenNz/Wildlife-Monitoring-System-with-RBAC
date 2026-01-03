<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Link ke zona berisiko (jika insiden terjadi di zona tertentu)
            $table->unsignedBigInteger('risk_zone_id')->nullable()->after('location');

            // Deskripsi lokasi lebih rinci
            $table->text('location_description')->nullable()->after('location');

            // Foreign key
            $table->foreign('risk_zone_id')->references('id')->on('risk_zones')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['risk_zone_id']);
            $table->dropColumn(['risk_zone_id', 'location_description']);
        });
    }
};
