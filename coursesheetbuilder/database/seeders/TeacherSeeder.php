<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the department
        $department = Department::where('name_hu', 'Gazdaságtudományi Tanszék')->first();

        // Create user for teacher 1
        $user1 = User::create([
            'name' => 'Dr. Pál László',
            'email' => 'pal.laszlo@university.edu',
            'password' => bcrypt('password123'),
        ]);

        // Create teacher 1
        Teacher::create([
            'user_id' => $user1->id,
            'department_id' => $department->id,
            'academic_degree' => 'dr.',
            'position' => 'Conf. univ.',
            'first_name' => 'László',
            'last_name' => 'Pál',
            'neptun_code' => 'PAL001',
            'phone' => '+40-266-123456',
            'office_location' => 'A épület, 204',
            'consultation_hours' => 'Hétfő 14-16, Szerda 10-12',
        ]);

        // Create user for teacher 2
        $user2 = User::create([
            'name' => 'Lect. univ. dr. Kovács Anna',
            'email' => 'kovacs.anna@university.edu',
            'password' => bcrypt('password123'),
        ]);

        // Create teacher 2
        Teacher::create([
            'user_id' => $user2->id,
            'department_id' => $department->id,
            'academic_degree' => 'dr.',
            'position' => 'Lect. univ.',
            'first_name' => 'Anna',
            'last_name' => 'Kovács',
            'neptun_code' => 'KOV001',
            'phone' => '+40-266-654321',
            'office_location' => 'B épület, 305',
            'consultation_hours' => 'Kedd 10-12, Csütörtök 14-16',
        ]);
    }
}
