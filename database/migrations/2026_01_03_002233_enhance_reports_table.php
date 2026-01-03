<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Format output (pdf, csv, geojson)
            $table->enum('output_format', ['pdf', 'csv', 'geojson'])->nullable()->after('report_type');

            // Siapa penerima laporan (selain pembuat)
            $table->unsignedBigInteger('generated_for_user_id')->nullable()->after('generated_by');

            // Foreign key ke users
            $table->foreign('generated_for_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['generated_for_user_id']);
            $table->dropColumn(['output_format', 'generated_for_user_id']);
        });
    }
};
