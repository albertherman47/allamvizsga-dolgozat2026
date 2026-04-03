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
        Schema::create('course_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_course_id')
                ->unique()
                ->constrained('curriculum_courses')
                ->onDelete('cascade');
            $table->foreignId('course_leader_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->foreignId('seminar_leader_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->foreignId('lab_leader_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->foreignId('project_leader_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('course_leader_id');
            $table->index('seminar_leader_id');
            $table->index('lab_leader_id');
            $table->index('project_leader_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_assignments');
    }
};
