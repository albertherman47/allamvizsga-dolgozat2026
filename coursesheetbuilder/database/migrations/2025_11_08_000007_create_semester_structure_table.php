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
        Schema::create('semester_structure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curricula')->onDelete('cascade');
            $table->integer('semester_number');
            $table->integer('weeks_count')->nullable();
            $table->integer('weekly_hours')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('curriculum_id');
            $table->unique(['curriculum_id', 'semester_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester_structure');
    }
};
