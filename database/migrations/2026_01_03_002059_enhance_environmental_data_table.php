<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('environmental_data', function (Blueprint $table) {
            // Pastikan kolom koordinat & waktu ada (jika belum)
            if (!Schema::hasColumn('environmental_data', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('environmental_data', 'longitude', 11, 8)) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('environmental_data', 'recorded_at')) {
                $table->timestamp('recorded_at')->nullable();
            }
        });
    }

    public function down()
    {
        // Opsional: jangan hapus kolom jika sudah digunakan
    }
};
