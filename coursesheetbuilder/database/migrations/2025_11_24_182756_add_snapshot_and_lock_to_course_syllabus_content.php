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
            $table->json('completed_snapshot')->nullable()->after('editable_data');
            $table->timestamp('completed_at')->nullable()->after('status');
            $table->boolean('is_locked')->default(false)->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_syllabus_content', function (Blueprint $table) {
            $table->dropColumn(['completed_snapshot', 'completed_at', 'is_locked']);
        });
    }
};
