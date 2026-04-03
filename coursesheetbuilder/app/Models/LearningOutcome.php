<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningOutcome extends Model
{
    use HasFactory;

    protected $table = 'learning_outcomes';

    protected $fillable = [
        'curriculum_course_id',
        'outcome_type',
        'description',
        'display_order',
    ];

    /**
     * Get the curriculum course this outcome belongs to
     */
    public function curriculumCourse(): BelongsTo
    {
        return $this->belongsTo(CurriculumCourse::class);
    }
}
