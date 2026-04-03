<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the teacher profile associated with this user
     */
    public function teacher(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get the administrative staff profile associated with this user
     */
    public function administrativeStaff(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdministrativeStaff::class);
    }

    /**
     * Check if this user is a teacher
     */
    public function isTeacher(): bool
    {
        return $this->teacher()->exists();
    }

    /**
     * Check if this user is administrative staff
     */
    public function isAdministrativeStaff(): bool
    {
        return $this->administrativeStaff()->exists();
    }
}
