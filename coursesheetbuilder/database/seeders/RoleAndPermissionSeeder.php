<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing permissions and roles cache
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions
        $permissions = [
            // Syllabus Management (legfontosabb)
            'view_own_syllabus',
            'edit_own_syllabus',
            'view_department_syllabus',      // Dept admin: saját tanszék
            'view_all_syllabus',             // Super admin: minden
            'edit_all_syllabus',             // Super admin: minden

            // Course Assignments
            'view_own_assignments',
            'view_department_assignments',
            'view_all_assignments',
            'manage_assignments',

            // Documents
            'generate_own_documents',
            'view_own_documents',
            'view_department_documents',
            'view_all_documents',

            // Management (Department Admin + Super Admin)
            'manage_own_department',          // Dept admin: saját tanszék
            'manage_all_departments',         // Super admin: minden tanszék
            'manage_teachers',
            'manage_programs',
            'manage_curricula',
            'manage_courses',

            // System (csak Super Admin)
            'manage_users',
            'manage_roles',
            'manage_templates',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Define roles and their permissions
        $superAdminPermissions = [
            'view_own_syllabus',
            'edit_own_syllabus',
            'view_department_syllabus',
            'view_all_syllabus',
            'edit_all_syllabus',
            'view_own_assignments',
            'view_department_assignments',
            'view_all_assignments',
            'manage_assignments',
            'generate_own_documents',
            'view_own_documents',
            'view_department_documents',
            'view_all_documents',
            'manage_own_department',
            'manage_all_departments',
            'manage_teachers',
            'manage_programs',
            'manage_curricula',
            'manage_courses',
            'manage_users',
            'manage_roles',
            'manage_templates',
        ];

        $departmentAdminPermissions = [
            'view_own_syllabus',
            'edit_own_syllabus',
            'view_department_syllabus',
            'view_own_assignments',
            'view_department_assignments',
            'manage_assignments',
            'generate_own_documents',
            'view_own_documents',
            'view_department_documents',
            'manage_own_department',
            'manage_teachers',
            'manage_programs',
            'manage_curricula',
            'manage_courses',
        ];

        $teacherPermissions = [
            'view_own_syllabus',
            'edit_own_syllabus',
            'view_own_assignments',
            'generate_own_documents',
            'view_own_documents',
        ];

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($superAdminPermissions);

        $departmentAdminRole = Role::firstOrCreate(['name' => 'department_admin', 'guard_name' => 'web']);
        $departmentAdminRole->syncPermissions($departmentAdminPermissions);

        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacherRole->syncPermissions($teacherPermissions);
    }
}
