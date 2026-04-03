<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Curriculum;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurriculumSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $program = Program::where('code', 'CBGECO')->first();
        $academicYear = AcademicYear::where('year_code', '2025/26')->first();

        Curriculum::create([
            'program_id' => $program->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Plan de învățământ - Informatică economică 2025/26',
            'hours_per_credit' => 28,
        ]);
    }
}
