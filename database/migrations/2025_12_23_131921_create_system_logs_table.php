<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['info', 'warning', 'error', 'debug']);
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::table('system_logs', function (Blueprint $table) {
            $table->index(['level', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
