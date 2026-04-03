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
        Schema::table('course_syllabus_content', function (Blueprint $table) {
            $table->enum('status', ['draft', 'completed'])->default('draft')->after('editable_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_syllabus_content', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
