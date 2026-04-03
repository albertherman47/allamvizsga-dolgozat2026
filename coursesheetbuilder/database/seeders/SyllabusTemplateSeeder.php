<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SyllabusTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SyllabusTemplateSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the 2025/26 academic year
        $academicYear = AcademicYear::where('year_code', '2025/26')->firstOrFail();

        // Read the placeholders config from JSON file
        $configPath = base_path('placeholders_config.json');
        $placeholdersConfig = json_decode(file_get_contents($configPath), true);

        // Create the syllabus template for 2025/26
        SyllabusTemplate::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Fișa disciplinei 2025/26',
            'docx_template_path' => 'template_fisa_disciplinei_2025.docx',
            'placeholders_config' => $placeholdersConfig,
            'is_active' => true,
        ]);
    }
}
