<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('risk_zones', function (Blueprint $table) {
            // Kolom deskripsi zona

            // Siapa yang membuat zona (audit trail)
            $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');

            // Status aktif/nonaktif (soft deactivate)
            $table->boolean('is_active')->default(true)->after('updated_at');

            // Foreign key ke users
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('risk_zones', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['description', 'created_by', 'is_active']);
        });
    }
};
