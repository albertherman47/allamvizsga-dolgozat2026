<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdministrativeStaff extends Model
{
    use HasFactory;

    protected $table = 'administrative_staff';

    protected $fillable = [
        'user_id',
        'department_id',
        'first_name',
        'last_name',
        'phone',
        'office_location',
        'staff_type',
        'responsibilities',
    ];

    /**
     * Get the user associated with this administrative staff
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department this staff belongs to (nullable)
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the full name of the staff member
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
