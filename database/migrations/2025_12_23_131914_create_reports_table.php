<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->string('report_type');
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('generated_at');
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps(); // opsional, tapi generated_at lebih akurat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
