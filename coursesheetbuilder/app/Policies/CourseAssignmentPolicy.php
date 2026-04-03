<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CourseAssignment;

class CourseAssignmentPolicy
{
    /**
     * Determine if user can view their own assignments
     */
    public function viewOwn(User $user, CourseAssignment $assignment): bool
    {
        return $user->hasPermissionTo('view_own_assignments') &&
               $user->teacher?->courseAssignments()
                   ->where('id', $assignment->id)
                   ->exists();
    }

    /**
     * Determine if user can view department assignments
     */
    public function viewDepartment(User $user, CourseAssignment $assignment): bool
    {
        return $user->hasPermissionTo('view_department_assignments') &&
               $user->teacher?->department_id === $assignment->curriculumCourse->curriculum->program->department_id;
    }

    /**
     * Determine if user can view all assignments
     */
    public function viewAll(User $user): bool
    {
        return $user->hasPermissionTo('view_all_assignments');
    }

    /**
     * Determine if user can manage assignments
     */
    public function manage(User $user): bool
    {
        return $user->hasPermissionTo('manage_assignments');
    }
}
