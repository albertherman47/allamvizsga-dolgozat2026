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
        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curricula')->onDelete('cascade');
            $table->string('course_code', 20);
            $table->string('course_name_hu');
            $table->string('course_name_ro');
            $table->string('course_name_en');
            $table->integer('study_year');
            $table->integer('semester');
            $table->integer('credits');
            $table->integer('lecture_hours');
            $table->integer('lecture_hours_online')->default(0);
            $table->integer('seminar_hours')->default(0);
            $table->integer('seminar_hours_online')->default(0);
            $table->integer('lab_hours')->default(0);
            $table->integer('lab_hours_online')->default(0);
            $table->integer('project_hours')->default(0);
            $table->integer('project_hours_online')->default(0);
            $table->enum('course_type', ['DOB', 'DOP', 'DFA']);
            $table->enum('formative_category', ['DF', 'DS', 'DC']);
            $table->enum('exam_type', ['E', 'C', 'VP', 'A/R']);
            $table->string('activity_type')->nullable();
            $table->json('learning_outcomes_knowledge')->nullable();
            $table->json('learning_outcomes_skills')->nullable();
            $table->json('learning_outcomes_responsibility')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('curriculum_id');
            $table->index('course_code');
            $table->index(['study_year', 'semester']);
            $table->index('course_type');
            $table->index('exam_type');
            $table->unique(['curriculum_id', 'course_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_courses');
    }
};
