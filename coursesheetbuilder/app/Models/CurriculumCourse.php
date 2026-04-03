<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumCourse extends Model
{
    use HasFactory;

    protected $table = 'curriculum_courses';

    protected $fillable = [
        'curriculum_id',
        'course_code',
        'course_name_hu',
        'course_name_ro',
        'course_name_en',
        'study_year',
        'semester',
        'credits',
        'lecture_hours',
        'lecture_hours_online',
        'seminar_hours',
        'seminar_hours_online',
        'lab_hours',
        'lab_hours_online',
        'project_hours',
        'project_hours_online',
        'course_type',
        'formative_category',
        'master_category',
        'exam_type',
        'activity_type',
        'learning_outcomes_knowledge',
        'learning_outcomes_skills',
        'learning_outcomes_responsibility',
    ];

    protected $casts = [
        'learning_outcomes_knowledge' => 'array',
        'learning_outcomes_skills' => 'array',
        'learning_outcomes_responsibility' => 'array',
    ];

    /**
     * Get the curriculum this course belongs to
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    /**
     * Get all learning outcomes for this course
     */
    public function learningOutcomes(): HasMany
    {
        return $this->hasMany(LearningOutcome::class);
    }

    /**
     * Get all course assignments for this course
     */
    public function courseAssignments(): HasMany
    {
        return $this->hasMany(CourseAssignment::class);
    }

    /**
     * Get the total hours for this course
     */
    public function getTotalHoursAttribute(): int
    {
        return $this->lecture_hours + $this->seminar_hours + $this->lab_hours + $this->project_hours;
    }

    /**
     * Get the total online hours for this course
     */
    public function getTotalOnlineHoursAttribute(): int
    {
        return $this->lecture_hours_online + $this->seminar_hours_online + $this->lab_hours_online + $this->project_hours_online;
    }
}
