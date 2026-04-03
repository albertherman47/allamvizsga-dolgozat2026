<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create([
            'name_hu' => 'Gazdaságtudományi Tanszék',
            'name_ro' => 'Catedra de Științe Economice',
            'name_en' => 'Department of Economic Sciences',
            'head_name' => 'Dr. László Pál',
        ]);
    }
}
