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
        Schema::create('generated_syllabi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_assignment_id')
                ->constrained('course_assignments')
                ->onDelete('cascade');
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->onDelete('cascade');
            $table->string('file_path', 255)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->integer('file_size')->nullable();
            $table->string('file_hash', 64)->nullable();
            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('teachers')
                ->onDelete('set null');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('template_id')
                ->nullable()
                ->constrained('syllabus_templates')
                ->onDelete('set null');
            $table->integer('version')->default(1);
            $table->boolean('is_latest')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_syllabi');
    }
};
