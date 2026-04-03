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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
            $table->enum('academic_degree', ['dr.', 'drd.', 'dr. habil.'])->nullable();
            $table->enum('position', ['Prof. univ.', 'Conf. univ.', 'Lect. univ.', 'Asist. univ.']);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('neptun_code', 20)->unique();
            $table->string('phone', 50)->nullable();
            $table->string('office_location')->nullable();
            $table->text('consultation_hours')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('department_id');
            $table->index('neptun_code');
            $table->index(['last_name', 'first_name']);
            $table->index('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
