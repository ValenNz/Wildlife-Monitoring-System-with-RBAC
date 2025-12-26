<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('User who reported');
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
            $table->unsignedBigInteger('animal_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('Assigned user');
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('severity');
            $table->index('status');
            $table->index('animal_id');
            $table->index('assigned_to');
            $table->index('reported_at');

            // Foreign keys (optional - uncomment if you want strict FK)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('animal_id')->references('id')->on('animals')->onDelete('set null');
            // $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
