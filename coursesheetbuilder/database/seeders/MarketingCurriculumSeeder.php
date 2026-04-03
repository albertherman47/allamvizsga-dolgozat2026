<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\CourseAssignment;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\Department;
use App\Models\Program;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketingCurriculumSeeder extends Seeder
{
    public function run(): void
    {
        // Use existing Department (from FullCurriculumSeeder)
        $department = Department::where('name_hu', 'Gazdaságtudományi Tanszék')->first();
        if (!$department) {
            $department = Department::create([
                'name_hu' => 'Gazdaságtudományi Tanszék',
                'name_ro' => 'Catedra de Științe Economice',
                'name_en' => 'Department of Economic Sciences',
                'head_name' => 'Dr. László Pál',
            ]);
        }

        
        $academicYear = AcademicYear::where('year_code', '2025/26')->first();
        if (!$academicYear) {
            $academicYear = AcademicYear::create([
                'year_code' => '2025/26',
                'start_year' => 2025,
                'end_year' => 2026,
            ]);
        }

        
        $userSeer = User::firstOrCreate(
            ['email' => 'seer.laszlo@uni.sapientia.ro'],
            [
                'name' => 'Dr. Seer László',
                'password' => bcrypt('password123'),
            ]
        );
        $userSeer->assignRole('teacher');

        $userKadar = User::firstOrCreate(
            ['email' => 'kadar.beata@uni.sapientia.ro'],
            [
                'name' => 'Dr. Kádár Beáta',
                'password' => bcrypt('password123'),
            ]
        );
        $userKadar->assignRole('teacher');

        
        $teacherSeer = Teacher::firstOrCreate(
            ['user_id' => $userSeer->id],
            [
                'department_id' => $department->id,
                'academic_degree' => 'dr.',
                'position' => 'Conf. univ.',
                'first_name' => 'László',
                'last_name' => 'Seer',
                'neptun_code' => 'SEE001',
                'phone' => '+40-266-999001',
                'office_location' => 'B épület',
                'consultation_hours' => 'Kedd 10-12',
            ]
        );

        $teacherKadar = Teacher::firstOrCreate(
            ['user_id' => $userKadar->id],
            [
                'department_id' => $department->id,
                'academic_degree' => 'dr.',
                'position' => 'Lect. univ.',
                'first_name' => 'Beáta',
                'last_name' => 'Kádár',
                'neptun_code' => 'KAD001',
                'phone' => '+40-266-999002',
                'office_location' => 'B épület',
                'consultation_hours' => 'Szerda 14-16',
            ]
        );

        // Update Marketing Program
        $program = Program::updateOrCreate(
            ['code' => 'CBMARK'],
            [
                'department_id' => $department->id,
                'name_hu' => 'Marketing',
                'name_ro' => 'Marketing',
                'name_en' => 'Marketing',
                'domain' => 'Marketing',
                'cycle' => 'Licență',
                'qualification' => 'Marketing',
                'coordinator_id' => $teacherSeer->id,
                'program_manager_id' => $teacherKadar->id,
            ]
        );

        // Create Curriculum
        $curriculum = Curriculum::firstOrCreate(
            [
                'program_id' => $program->id,
                'academic_year_id' => $academicYear->id,
            ],
            [
                'name' => 'Plan de învățământ - Marketing 2025/26',
                'hours_per_credit' => 28,
            ]
        );

        // Read courses from JSON file
        $jsonPath = base_path('marketing_curriculum_data.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error("marketing_curriculum_data.json not found!");
            return;
        }

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        // Create all courses & configure assignments
        foreach ($jsonData['courses'] as $courseData) {
            $course = CurriculumCourse::updateOrCreate(
                [
                    'curriculum_id' => $curriculum->id,
                    'course_code' => $courseData['code'],
                ],
                [
                    'course_name_hu' => $courseData['name_hu'],
                    'course_name_ro' => $courseData['name_ro'],
                    'course_name_en' => $courseData['name_en'],
                    'study_year' => $courseData['year'],
                    'semester' => $courseData['semester'],
                    'credits' => $courseData['credits'],
                    'lecture_hours' => $courseData['lecture_hours'],
                    'lecture_hours_online' => 0,
                    'seminar_hours' => $courseData['seminar_hours'],
                    'seminar_hours_online' => 0,
                    'lab_hours' => $courseData['lab_hours'],
                    'lab_hours_online' => 0,
                    'project_hours' => $courseData['project_hours'],
                    'project_hours_online' => 0,
                    'course_type' => $courseData['course_type'],
                    'formative_category' => $courseData['category'],
                    'exam_type' => $courseData['exam_type'],
                    'activity_type' => null,
                    'learning_outcomes_knowledge' => $courseData['learning_outcomes']['knowledge'] ?? null,
                    'learning_outcomes_skills' => $courseData['learning_outcomes']['skills'] ?? null,
                    'learning_outcomes_responsibility' => $courseData['learning_outcomes']['responsibility_autonomy'] ?? null,
                ]
            );

            // Assign teachers based on course code
            $leaderId = null;
            $seminarLeaderId = null;

            if (in_array($courseData['code'], ['CBMR0101', 'CBMR0301', 'CBMR0201'])) {
                $leaderId = $teacherSeer->id;
                $seminarLeaderId = $teacherSeer->id;
            } elseif (in_array($courseData['code'], ['CBMR0401', 'CBMR0501'])) {
                $leaderId = $teacherKadar->id;
                $seminarLeaderId = $teacherKadar->id;
            }

            CourseAssignment::updateOrCreate(
                ['curriculum_course_id' => $course->id],
                [
                    'course_leader_id' => $leaderId,
                    'seminar_leader_id' => $seminarLeaderId,
                ]
            );
        }
        
        $this->command->info("Marketing program, real teachers, and curriculum seeded successfully!");
    }
}
