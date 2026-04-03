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
        // Syllabus Templates
        if (!Schema::hasTable('syllabus_templates')) {
            Schema::create('syllabus_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
                $table->string('name', 100); // 'Fișa disciplinei 2025/26'
                $table->string('docx_template_path', 255)->nullable(); // storage path
                $table->json('placeholders_config')->nullable(); // teljes metadata
                $table->json('form_config')->nullable(); // Filament form config
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                // Indexes
                $table->index('academic_year_id');
                $table->index('is_active');
            });
        }

        // Course Syllabus Content
        if (!Schema::hasTable('course_syllabus_content')) {
            Schema::create('course_syllabus_content', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_assignment_id')->constrained('course_assignments')->onDelete('cascade');
                $table->foreignId('template_id')->constrained('syllabus_templates')->onDelete('cascade');
                $table->json('editable_data')->nullable(); // szerkeszthető mezők
                $table->integer('version')->default(1);
                $table->timestamps();

                // Unique constraint
                $table->unique('course_assignment_id');
                // Indexes
                $table->index('template_id');
            });
        }

        // Generated Syllabi
        if (!Schema::hasTable('generated_syllabi')) {
            Schema::create('generated_syllabi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_assignment_id')->constrained('course_assignments')->onDelete('cascade');
                $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
                $table->string('file_path', 255)->nullable(); // storage path
                $table->string('file_name', 255)->nullable(); // display name
                $table->integer('file_size')->nullable(); // bytes
                $table->string('file_hash', 64)->nullable(); // SHA256
                $table->foreignId('generated_by')->nullable()->constrained('teachers')->onDelete('set null');
                $table->timestamp('generated_at')->nullable();
                $table->foreignId('template_id')->nullable()->constrained('syllabus_templates')->onDelete('set null');
                $table->integer('version')->default(1);
                $table->boolean('is_latest')->default(true);
                $table->timestamps();

                // Indexes
                $table->index('course_assignment_id');
                $table->index('academic_year_id');
                $table->index('template_id');
                $table->index(['course_assignment_id', 'is_latest']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_syllabi');
        Schema::dropIfExists('course_syllabus_content');
        Schema::dropIfExists('syllabus_templates');
    }
};
