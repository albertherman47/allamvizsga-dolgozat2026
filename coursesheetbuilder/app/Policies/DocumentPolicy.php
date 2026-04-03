<?php

namespace App\Policies;

use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine if user can generate their own documents
     */
    public function generateOwn(User $user): bool
    {
        return $user->hasPermissionTo('generate_own_documents');
    }

    /**
     * Determine if user can view their own documents
     */
    public function viewOwn(User $user): bool
    {
        return $user->hasPermissionTo('view_own_documents');
    }

    /**
     * Determine if user can view department documents
     */
    public function viewDepartment(User $user): bool
    {
        return $user->hasPermissionTo('view_department_documents');
    }

    /**
     * Determine if user can view all documents
     */
    public function viewAll(User $user): bool
    {
        return $user->hasPermissionTo('view_all_documents');
    }
}
