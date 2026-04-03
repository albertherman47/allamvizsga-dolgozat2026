<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $table = 'academic_years';

    protected $fillable = [
        'year_code',
        'start_year',
        'end_year',
        'hours_per_credit',
    ];

    /**
     * Get all curricula for this academic year
     */
    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    /**
     * Get all course assignments for this academic year
     */
    public function courseAssignments(): HasMany
    {
        return $this->hasMany(CourseAssignment::class);
    }

    /**
     * Get all syllabus templates for this academic year
     */
    public function syllabusTemplates(): HasMany
    {
        return $this->hasMany(SyllabusTemplate::class);
    }
}
