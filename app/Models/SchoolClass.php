<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'campus_id',
        'academic_year_id',
        'programme_id',
        'name',
        'level',
        'class_teacher_id',
        'report_card_theme',
        'capacity',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    public function streams()
    {
        return $this->hasMany(Stream::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'current_class_id');
    }
}
