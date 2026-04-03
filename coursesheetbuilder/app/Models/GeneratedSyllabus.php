<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedSyllabus extends Model
{
    use HasFactory;

    protected $table = 'generated_syllabi';

    protected $fillable = [
        'course_assignment_id',
        'academic_year_id',
        'file_path',
        'file_name',
        'file_size',
        'file_hash',
        'generated_by',
        'generated_at',
        'template_id',
        'version',
        'is_latest',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'is_latest' => 'boolean',
    ];

    /**
     * Get the course assignment for this generated syllabus
     */
    public function courseAssignment(): BelongsTo
    {
        return $this->belongsTo(CourseAssignment::class);
    }

    /**
     * Get the academic year
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the teacher who generated it
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'generated_by');
    }

    /**
     * Get the template used
     */
    public function syllabusTemplate(): BelongsTo
    {
        return $this->belongsTo(SyllabusTemplate::class, 'template_id');
    }
}
