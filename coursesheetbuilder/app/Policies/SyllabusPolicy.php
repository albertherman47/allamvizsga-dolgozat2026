<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CurriculumCourse;

class SyllabusPolicy
{
    /**
     * Determine if the user can view their own syllabus
     */
    public function viewOwn(User $user, CurriculumCourse $course): bool
    {
        // User can view if they have permission and are assigned to the course
        return $user->hasPermissionTo('view_own_syllabus') &&
               $user->teacher?->courseAssignments()
                   ->where('curriculum_course_id', $course->id)
                   ->exists();
    }

    /**
     * Determine if the user can edit their own syllabus
     */
    public function editOwn(User $user, CurriculumCourse $course): bool
    {
        return $user->hasPermissionTo('edit_own_syllabus') &&
               $user->teacher?->courseAssignments()
                   ->where('curriculum_course_id', $course->id)
                   ->exists();
    }

    /**
     * Determine if user can view department syllabus
     */
    public function viewDepartment(User $user, CurriculumCourse $course): bool
    {
        // Department admin can view all syllabi in their department
        return $user->hasPermissionTo('view_department_syllabus') &&
               $user->teacher?->department_id === $course->curriculum->program->department_id;
    }

    /**
     * Determine if user can view all syllabi
     */
    public function viewAll(User $user): bool
    {
        return $user->hasPermissionTo('view_all_syllabus');
    }

    /**
     * Determine if user can edit all syllabi
     */
    public function editAll(User $user): bool
    {
        return $user->hasPermissionTo('edit_all_syllabus');
    }
}
