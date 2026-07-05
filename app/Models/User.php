<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, BelongsToSchool;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'school_id',
        'campus_id',
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'profile_photo',
        'employee_id',
        'gender',
        'date_of_birth',
        'address',
        'is_active',
        'mfa_secret',
        'mfa_enabled',
        'last_login_at',
        'last_login_ip',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
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
            'is_active' => 'boolean',
            'mfa_enabled' => 'boolean',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Check if user has a specific permission slug.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->role && $this->role->slug === 'super-admin') {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        // Use relation property to leverage dynamic property loading/caching of collection in-memory.
        // This avoids query builder calls (which would do a database SELECT exists query for every check).
        return $this->role->permissions->contains('slug', $permissionSlug);
    }
}
