<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curriculum extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'academic_year_id',
        'name',
        'hours_per_credit',
    ];

    /**
     * Get the program this curriculum belongs to
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the academic year of this curriculum
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get all semester structures for this curriculum
     */
    public function semesterStructures(): HasMany
    {
        return $this->hasMany(SemesterStructure::class);
    }

    /**
     * Get all courses in this curriculum
     */
    public function courses(): HasMany
    {
        return $this->hasMany(CurriculumCourse::class);
    }
}
