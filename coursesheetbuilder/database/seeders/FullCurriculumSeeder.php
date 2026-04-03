<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\CourseAssignment;
use App\Models\CourseSyllabusContent;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\Department;
use App\Models\Program;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class  FullCurriculumSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Department
        $department = Department::create([
            'name_hu' => 'Gazdaságtudományi Tanszék',
            'name_ro' => 'Catedra de Științe Economice',
            'name_en' => 'Department of Economic Sciences',
            'head_name' => 'Dr. László Pál',
        ]);

        // Create users for teachers
        $user1 = User::create([
            'name' => 'Dr. Pál László',
            'email' => 'pal.laszlo@university.edu',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Lect. univ. dr. Kovács Anna',
            'email' => 'kovacs.anna@university.edu',
            'password' => bcrypt('password123'),
        ]);

        // Create teachers
        $palLaszlo = Teacher::create([
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

        $kovacsAnna = Teacher::create([
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

        // Assign teacher roles
        $user1->assignRole('teacher');
        $user2->assignRole('teacher');

        // Create Academic Year
        $academicYear = AcademicYear::create([
            'year_code' => '2025/26',
            'start_year' => 2025,
            'end_year' => 2026,
        ]);

        // Create Program
        $program = Program::create([
            'department_id' => $department->id,
            'code' => 'CBGECO',
            'name_hu' => 'Gazdasági informatika',
            'name_ro' => 'Informatică economică',
            'name_en' => 'Economic Informatics',
            'domain' => 'Cibernetică, Statistică și Informatică Economică',
            'cycle' => 'Licență',
            'qualification' => 'Informatică economică',
            'coordinator_id' => $palLaszlo->id,
            'program_manager_id' => $kovacsAnna->id,
        ]);

        // Create Curriculum
        $curriculum = Curriculum::create([
            'program_id' => $program->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Plan de învățământ - Informatică economică 2025/26',
            'hours_per_credit' => 28,
        ]);

        // Read courses from JSON file
        $jsonPath = base_path('curriculum_data.json');
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        // Create all courses
        $courses = [];
        foreach ($jsonData['courses'] as $courseData) {
            $course = CurriculumCourse::create([
                'curriculum_id' => $curriculum->id,
                'course_code' => $courseData['code'],
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
                // Store learning outcomes in curriculum_courses table
                'learning_outcomes_knowledge' => $courseData['learning_outcomes']['knowledge'] ?? null,
                'learning_outcomes_skills' => $courseData['learning_outcomes']['skills'] ?? null,
                'learning_outcomes_responsibility' => $courseData['learning_outcomes']['responsibility_autonomy'] ?? null,
            ]);

            $courses[$courseData['code']] = $course;
        }

        // Assign teachers to specific courses
        // Pál László as course leader for Web Programming, Java Programming, Web Technologies, Mobile Applications
        $coursesToAssign = ['CBEI0241', 'CBEI0430', 'CBEI0261', 'CBEI0471'];

        foreach ($coursesToAssign as $courseCode) {
            if (isset($courses[$courseCode])) {
                CourseAssignment::create([
                    'curriculum_course_id' => $courses[$courseCode]->id,
                    'course_leader_id' => $palLaszlo->id,
                    'seminar_leader_id' => null,
                    'lab_leader_id' => null,
                    'project_leader_id' => null,
                ]);
            }
        }

        // Assign teachers to other courses (Kovács Anna as course leader for some courses)
        $otherCourses = [
            'CBEM0311' => ['course_leader' => $kovacsAnna],
            'CBGG0761' => ['course_leader' => $kovacsAnna],
        ];

        foreach ($otherCourses as $courseCode => $assignment) {
            if (isset($courses[$courseCode])) {
                CourseAssignment::create([
                    'curriculum_course_id' => $courses[$courseCode]->id,
                    'course_leader_id' => $assignment['course_leader']->id,
                    'seminar_leader_id' => null,
                    'lab_leader_id' => null,
                    'project_leader_id' => null,
                ]);
            }
        }
    }
}
