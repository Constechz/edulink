<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'class_id',
        'name',
        'class_teacher_id',
        'capacity',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'current_stream_id');
    }
}
