<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\CourseAssignment;
use App\Models\CurriculumCourse;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurriculumCourseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $curriculum = Curriculum::whereHas('program', function ($query) {
            $query->where('code', 'CBGECO');
        })->first();

        // Get teachers
        $pal = Teacher::where('neptun_code', 'PAL001')->first();
        $kovacs = Teacher::where('neptun_code', 'KOV001')->first();

        // Create Course 1: Programare Web
        $course1 = CurriculumCourse::create([
            'curriculum_id' => $curriculum->id,
            'course_code' => 'CBEI0241',
            'course_name_hu' => 'Webprogramozás',
            'course_name_ro' => 'Programare Web',
            'course_name_en' => 'Web Programming',
            'study_year' => 3,
            'semester' => 5,
            'credits' => 5,
            'lecture_hours' => 2,
            'lecture_hours_online' => 0,
            'seminar_hours' => 0,
            'seminar_hours_online' => 0,
            'lab_hours' => 2,
            'lab_hours_online' => 0,
            'project_hours' => 0,
            'project_hours_online' => 0,
            'course_type' => 'DOB',
            'formative_category' => 'DS',
            'exam_type' => 'E',
            'activity_type' => 'Asistat integral',
        ]);

        // Create Course 2: Technologii Web
        $course2 = CurriculumCourse::create([
            'curriculum_id' => $curriculum->id,
            'course_code' => 'CBEI0242',
            'course_name_hu' => 'Webtechnológiák',
            'course_name_ro' => 'Tehnologii Web',
            'course_name_en' => 'Web Technologies',
            'study_year' => 3,
            'semester' => 6,
            'credits' => 4,
            'lecture_hours' => 2,
            'lecture_hours_online' => 0,
            'seminar_hours' => 1,
            'seminar_hours_online' => 0,
            'lab_hours' => 2,
            'lab_hours_online' => 0,
            'project_hours' => 0,
            'project_hours_online' => 0,
            'course_type' => 'DOP',
            'formative_category' => 'DS',
            'exam_type' => 'C',
            'activity_type' => 'Asistat integral',
        ]);

        // Assign teachers to courses
        // Course 1: Pál as course leader, Kovács as lab leader
        CourseAssignment::create([
            'curriculum_course_id' => $course1->id,
            'academic_year_id' => $curriculum->academic_year_id,
            'course_leader_id' => $pal->id,
            'seminar_leader_id' => null,
            'lab_leader_id' => $kovacs->id,
            'project_leader_id' => null,
        ]);

        // Course 2: Kovács as course leader, Pál as seminar leader
        CourseAssignment::create([
            'curriculum_course_id' => $course2->id,
            'academic_year_id' => $curriculum->academic_year_id,
            'course_leader_id' => $kovacs->id,
            'seminar_leader_id' => $pal->id,
            'lab_leader_id' => $kovacs->id,
            'project_leader_id' => null,
        ]);
    }
}
