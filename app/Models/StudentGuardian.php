<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model
{
    protected $table = 'student_guardians';

    protected $fillable = [
        'student_id',
        'guardian_id',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];
}
