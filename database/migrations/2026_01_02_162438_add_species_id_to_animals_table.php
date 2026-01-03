<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->unsignedBigInteger('species_id')->after('id')->nullable();

            // Tambahkan foreign key constraint
            $table->foreign('species_id')
                  ->references('id')
                  ->on('species')
                  ->onDelete('set null'); // Opsional: 'restrict' jika tidak boleh null
        });
    }

    public function down()
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['species_id']);
            $table->dropColumn('species_id');
        });
    }
};
