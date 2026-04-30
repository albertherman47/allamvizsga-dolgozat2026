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
            FullCurriculumSeeder::class,   // 2025/26-os tanterv + program + tanárok
            Curriculum2024Seeder::class,   // 2024/25-os tanterv (ugyanazok a tárgyak)
            SyllabusTemplateSeeder::class,
        ]);
    }
}
