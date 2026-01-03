<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('animal_risk_zones', function (Blueprint $table) {
            // Durasi dalam menit
            $table->integer('duration_minutes')->nullable()->after('exited_at');

            // Status real-time: apakah satwa masih di zona?
            $table->boolean('is_currently_in_zone')->default(false)->after('duration_minutes');
        });
    }

    public function down()
    {
        Schema::table('animal_risk_zones', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes', 'is_currently_in_zone']);
        });
    }
};
