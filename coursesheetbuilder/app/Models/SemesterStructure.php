<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterStructure extends Model
{
    use HasFactory;

    protected $table = 'semester_structure';

    protected $fillable = [
        'curriculum_id',
        'semester_number',
        'weeks_count',
        'weekly_hours',
    ];

    /**
     * Get the curriculum this semester belongs to
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }
}
