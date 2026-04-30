<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\CourseAssignment;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\Program;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Curriculum2024Seeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Létrehozza a 2024/25-ös tanévi tantervet.
     * Ugyanazokat a tárgyakat tartalmazza mint a 2025/26-os tanterv,
     * de külön AcademicYear és Curriculum rekordként kerül be az adatbázisba.
     */
    public function run(): void
    {
        // Lekérjük az már meglévő programot (FullCurriculumSeeder hozta létre)
        $program = Program::where('code', 'CBGECO')->firstOrFail();

        // Lekérjük a már meglévő tanárokat
        $palLaszlo  = Teacher::where('neptun_code', 'PAL001')->firstOrFail();
        $kovacsAnna = Teacher::where('neptun_code', 'KOV001')->firstOrFail();

        // Létrehozzuk a 2024/25-ös tanévet
        $academicYear = AcademicYear::firstOrCreate(
            ['year_code' => '2024/25'],
            [
                'start_year' => 2024,
                'end_year'   => 2025,
            ]
        );

        // Létrehozzuk a 2024/25-ös tantervet
        $curriculum = Curriculum::create([
            'program_id'       => $program->id,
            'academic_year_id' => $academicYear->id,
            'name'             => 'Plan de învățământ - Informatică economică 2024/25',
            'hours_per_credit' => 28,
        ]);

        // Beolvassuk a tárgyakat ugyanabból a JSON-ből
        $jsonPath = base_path('curriculum_data.json');

        if (! file_exists($jsonPath)) {
            $this->command->error('curriculum_data.json not found!');
            return;
        }

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        // Létrehozzuk az összes tárgyat az új curriculum_id-vel
        $courses = [];
        foreach ($jsonData['courses'] as $courseData) {
            $course = CurriculumCourse::create([
                'curriculum_id'                    => $curriculum->id,
                'course_code'                      => $courseData['code'],
                'course_name_hu'                   => $courseData['name_hu'],
                'course_name_ro'                   => $courseData['name_ro'],
                'course_name_en'                   => $courseData['name_en'],
                'study_year'                       => $courseData['year'],
                'semester'                         => $courseData['semester'],
                'credits'                          => $courseData['credits'],
                'lecture_hours'                    => $courseData['lecture_hours'],
                'lecture_hours_online'             => 0,
                'seminar_hours'                    => $courseData['seminar_hours'],
                'seminar_hours_online'             => 0,
                'lab_hours'                        => $courseData['lab_hours'],
                'lab_hours_online'                 => 0,
                'project_hours'                    => $courseData['project_hours'],
                'project_hours_online'             => 0,
                'course_type'                      => $courseData['course_type'],
                'formative_category'               => $courseData['category'],
                'exam_type'                        => $courseData['exam_type'],
                'activity_type'                    => null,
                'learning_outcomes_knowledge'      => $courseData['learning_outcomes']['knowledge'] ?? null,
                'learning_outcomes_skills'         => $courseData['learning_outcomes']['skills'] ?? null,
                'learning_outcomes_responsibility' => $courseData['learning_outcomes']['responsibility_autonomy'] ?? null,
            ]);

            $courses[$courseData['code']] = $course;
        }

        // Pál László hozzárendelése: Webprogramozás, Java, Webtechnológiák, Mobil alkalmazások
        $coursesToAssignPal = ['CBEI0241', 'CBEI0430', 'CBEI0261', 'CBEI0471'];

        foreach ($coursesToAssignPal as $courseCode) {
            if (isset($courses[$courseCode])) {
                CourseAssignment::create([
                    'curriculum_course_id' => $courses[$courseCode]->id,
                    'course_leader_id'     => $palLaszlo->id,
                    'seminar_leader_id'    => null,
                    'lab_leader_id'        => null,
                    'project_leader_id'    => null,
                ]);
            }
        }

        // Kovács Anna hozzárendelése: Matematika, Gazdaság alapjai
        $otherCourses = [
            'CBEM0311' => $kovacsAnna,
            'CBGG0761' => $kovacsAnna,
        ];

        foreach ($otherCourses as $courseCode => $teacher) {
            if (isset($courses[$courseCode])) {
                CourseAssignment::create([
                    'curriculum_course_id' => $courses[$courseCode]->id,
                    'course_leader_id'     => $teacher->id,
                    'seminar_leader_id'    => null,
                    'lab_leader_id'        => null,
                    'project_leader_id'    => null,
                ]);
            }
        }

        $this->command->info('2024/25-ös tanterv sikeresen létrehozva!');
    }
}
