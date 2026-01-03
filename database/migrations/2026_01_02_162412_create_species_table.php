<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('common_name');           // Nama umum
            $table->string('scientific_name');        // Nama ilmiah
            $table->string('conservation_status');    // Contoh: "Critically Endangered"
            $table->timestamps();                     // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('species');
    }
};
