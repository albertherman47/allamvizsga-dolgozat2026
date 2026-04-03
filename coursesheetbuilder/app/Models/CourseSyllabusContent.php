<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSyllabusContent extends Model
{
    use HasFactory;

    protected $table = 'course_syllabus_content';

    protected $fillable = [
        'course_assignment_id',
        'template_id',
        'editable_data',
        'version',
        'status',
        'completed_snapshot',
        'completed_at',
        'is_locked',
    ];

    protected $casts = [
        'editable_data' => 'array',
        'completed_snapshot' => 'array',
        'completed_at' => 'datetime',
        'is_locked' => 'boolean',
        'status' => 'string',
    ];

    /**
     * Get the course assignment this syllabus belongs to
     */
    public function courseAssignment(): BelongsTo
    {
        return $this->belongsTo(CourseAssignment::class);
    }

    /**
     * Get the template this syllabus uses
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(SyllabusTemplate::class);
    }

    /**
     * Get all generated versions of this syllabus
     */
    public function generatedSyllabi(): HasMany
    {
        return $this->hasMany(GeneratedSyllabus::class, 'course_assignment_id', 'course_assignment_id');
    }

    /**
     * Scope to get content by course assignment
     */
    public function scopeByCourseAssignment($query, $courseAssignmentId)
    {
        return $query->where('course_assignment_id', $courseAssignmentId);
    }

    /**
     * Scope to get content by template
     */
    public function scopeByTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    /**
     * Check if syllabus is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get a specific editable section from the data
     */
    public function getEditableSection(string $section): ?array
    {
        return $this->editable_data[$section] ?? null;
    }

    /**
     * Check if course has laboratory component
     */
    public function hasLaboratory(): bool
    {
        return $this->courseAssignment?->curriculumCourse?->lab_hours > 0;
    }

    /**
     * Check if course has seminar component
     */
    public function hasSeminar(): bool
    {
        return $this->courseAssignment?->curriculumCourse?->seminar_hours > 0;
    }

    /**
     * Check if course has project component
     */
    public function hasProject(): bool
    {
        return $this->courseAssignment?->curriculumCourse?->project_hours > 0;
    }
}
