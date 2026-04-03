<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'code',
        'name_hu',
        'name_ro',
        'name_en',
        'domain',
        'cycle',
        'qualification',
        'coordinator_id',
        'program_manager_id',
    ];

    /**
     * Get the department this program belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the coordinator of this program
     */
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'coordinator_id');
    }

    /**
     * Get the program manager
     */
    public function programManager(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'program_manager_id');
    }

    /**
     * Get all curricula for this program
     */
    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }
}
