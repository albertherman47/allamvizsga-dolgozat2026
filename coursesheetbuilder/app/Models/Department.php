<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_hu',
        'name_ro',
        'name_en',
        'head_name',
    ];

    /**
     * Get all teachers in this department
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * Get all administrative staff in this department
     */
    public function administrativeStaff(): HasMany
    {
        return $this->hasMany(AdministrativeStaff::class);
    }

    /**
     * Get all programs in this department
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }
}
