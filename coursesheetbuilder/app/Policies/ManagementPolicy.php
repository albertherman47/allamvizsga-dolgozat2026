<?php

namespace App\Policies;

use App\Models\User;

class ManagementPolicy
{
    /**
     * Determine if user can manage teachers
     */
    public function manageTeachers(User $user): bool
    {
        return $user->hasPermissionTo('manage_teachers');
    }

    /**
     * Determine if user can manage programs
     */
    public function managePrograms(User $user): bool
    {
        return $user->hasPermissionTo('manage_programs');
    }

    /**
     * Determine if user can manage curricula
     */
    public function manageCurricula(User $user): bool
    {
        return $user->hasPermissionTo('manage_curricula');
    }

    /**
     * Determine if user can manage courses
     */
    public function manageCourses(User $user): bool
    {
        return $user->hasPermissionTo('manage_courses');
    }

    /**
     * Determine if user can manage users
     */
    public function manageUsers(User $user): bool
    {
        return $user->hasPermissionTo('manage_users');
    }

    /**
     * Determine if user can manage roles
     */
    public function manageRoles(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * Determine if user can manage templates
     */
    public function manageTemplates(User $user): bool
    {
        return $user->hasPermissionTo('manage_templates');
    }
}
