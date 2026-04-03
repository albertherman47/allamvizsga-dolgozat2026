<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $department = Department::where('name_hu', 'Gazdaságtudományi Tanszék')->first();

        Program::create([
            'department_id' => $department->id,
            'code' => 'CBGECO',
            'name_hu' => 'Gazdasági informatika',
            'name_ro' => 'Informatică economică',
            'name_en' => 'Economic Informatics',
            'domain' => 'Cibernetică, Statistică și Informatică Economică',
            'cycle' => 'Licență',
            'qualification' => 'Informatică economică',
            'coordinator_id' => null,
            'program_manager_id' => null,
        ]);
    }
}
