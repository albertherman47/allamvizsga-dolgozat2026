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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
            $table->string('code', 20)->unique();
            $table->string('name_hu');
            $table->string('name_ro');
            $table->string('name_en');
            $table->string('domain')->nullable();
            $table->enum('cycle', ['Licență', 'Master', 'Doctorat']);
            $table->integer('duration_years')->default(3);
            $table->integer('total_semesters')->default(6);
            $table->string('qualification')->nullable();
            $table->foreignId('coordinator_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->foreignId('program_manager_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('department_id');
            $table->index('code');
            $table->index('cycle');
            $table->index('coordinator_id');
            $table->index('program_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
