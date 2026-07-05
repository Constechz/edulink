<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'relationship',
        'phone',
        'alt_phone',
        'email',
        'occupation',
        'address',
        'is_primary',
        'photo',
        'can_pickup',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'can_pickup' => 'boolean',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_guardians');
    }
}
