<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;

class DepartmentPolicy
{
    /**
     * Determine if user can manage their own department
     */
    public function manageOwn(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('manage_own_department') &&
               $user->teacher?->department_id === $department->id;
    }

    /**
     * Determine if user can manage all departments
     */
    public function manageAll(User $user): bool
    {
        return $user->hasPermissionTo('manage_all_departments');
    }

    /**
     * Determine if user can view department
     */
    public function view(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('manage_own_department') ||
               $user->hasPermissionTo('manage_all_departments');
    }

    /**
     * Determine if user can update department
     */
    public function update(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('manage_own_department') && $user->teacher?->department_id === $department->id ||
               $user->hasPermissionTo('manage_all_departments');
    }

    /**
     * Determine if user can delete department
     */
    public function delete(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('manage_all_departments');
    }
}
