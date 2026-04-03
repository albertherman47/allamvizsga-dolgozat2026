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
        Schema::create('course_syllabus_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_assignment_id');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->unique('course_assignment_id');
            $table->json('editable_data')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();

            // Add foreign keys separately to ensure proper order
            $table->foreign('course_assignment_id')
                ->references('id')
                ->on('course_assignments')
                ->onDelete('cascade');
            $table->foreign('template_id')
                ->references('id')
                ->on('syllabus_templates')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_syllabus_content');
    }
};
