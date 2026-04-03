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
        Schema::table('curriculum_courses', function (Blueprint $table) {
            // Add master_category field after formative_category
            $table->enum('master_category', ['DA', 'DS'])->nullable()->after('formative_category')
                ->comment('DA = Disciplină de aprofundare, DS = Disciplină de sinteză (Master only)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curriculum_courses', function (Blueprint $table) {
            $table->dropColumn('master_category');
        });
    }
};
