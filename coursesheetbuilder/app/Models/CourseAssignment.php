<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseAssignment extends Model
{
    use HasFactory;

    protected $table = 'course_assignments';

    protected $fillable = [
        'curriculum_course_id',
        'course_leader_id',
        'seminar_leader_id',
        'lab_leader_id',
        'project_leader_id',
    ];

    /**
     * Get the curriculum course this assignment belongs to
     */
    public function curriculumCourse(): BelongsTo
    {
        return $this->belongsTo(CurriculumCourse::class);
    }


    /**
     * Get the course leader (lecture instructor)
     */
    public function courseLeader(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'course_leader_id');
    }

    /**
     * Get the seminar leader
     */
    public function seminarLeader(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'seminar_leader_id');
    }

    /**
     * Get the lab leader
     */
    public function labLeader(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'lab_leader_id');
    }

    /**
     * Get the project leader
     */
    public function projectLeader(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'project_leader_id');
    }

    /**
     * Get the syllabus content for this assignment
     */
    public function syllabusContent(): HasOne
    {
        return $this->hasOne(CourseSyllabusContent::class);
    }

    /**
     * Get all generated syllabi for this assignment
     */
    public function generatedSyllabi(): HasMany
    {
        return $this->hasMany(GeneratedSyllabus::class);
    }
}
