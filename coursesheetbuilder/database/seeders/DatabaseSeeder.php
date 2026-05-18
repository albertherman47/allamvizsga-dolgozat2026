<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in order
        $this->call([
            RoleAndPermissionSeeder::class,
            AdminUserSeeder::class,
            FullCurriculumSeeder::class,
            Curriculum2024Seeder::class,
            SyllabusTemplateSeeder::class,
        ]);
    }
}
