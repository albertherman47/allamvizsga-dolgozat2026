<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'academic_degree',
        'position',
        'first_name',
        'last_name',
        'neptun_code',
        'phone',
        'office_location',
        'consultation_hours',
    ];

    /**
     * Get the user associated with this teacher
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department this teacher belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get programs coordinated by this teacher
     */
    public function coordinatedPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'coordinator_id');
    }

    /**
     * Get programs managed by this teacher
     */
    public function managedPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'program_manager_id');
    }

    /**
     * Get course assignments as course leader
     */
    public function courseLeadAssignments(): HasMany
    {
        return $this->hasMany(CourseAssignment::class, 'course_leader_id');
    }

    /**
     * Get course assignments as seminar leader
     */
    public function seminarLeadAssignments(): HasMany
    {
        return $this->hasMany(CourseAssignment::class, 'seminar_leader_id');
    }

    /**
     * Get course assignments as lab leader
     */
    public function labLeadAssignments(): HasMany
    {
        return $this->hasMany(CourseAssignment::class, 'lab_leader_id');
    }

    /**
     * Get course assignments as project leader
     */
    public function projectLeadAssignments(): HasMany
    {
        return $this->hasMany(CourseAssignment::class, 'project_leader_id');
    }

    /**
     * Get the full name of the teacher
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get formatted teacher name with position, academic degree, and name
     * Format: [Position] [Academic Degree] LastName FirstName
     */
    public function getFormattedName(): string
    {
        $parts = [];

        // Add position if available
        if (!empty($this->position)) {
            $parts[] = $this->position;
        }

        // Add academic degree if available
        if (!empty($this->academic_degree)) {
            $parts[] = $this->academic_degree;
        }

        // Add name (last_name, first_name)
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';
        $fullName = trim("$lastName $firstName");
        if (!empty($fullName)) {
            $parts[] = $fullName;
        }

        return trim(implode(' ', $parts));
    }
}
