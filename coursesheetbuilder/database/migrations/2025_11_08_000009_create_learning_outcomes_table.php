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
        Schema::create('learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_course_id')->constrained('curriculum_courses')->onDelete('cascade');
            $table->enum('outcome_type', ['Cunoștințe', 'Aptitudini', 'Responsabilitate și autonomie']);
            $table->text('description');
            $table->integer('display_order')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('curriculum_course_id');
            $table->index('outcome_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_outcomes');
    }
};
