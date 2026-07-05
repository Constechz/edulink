<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use BelongsToSchool;

    protected $table = 'attendance_records';

    protected $fillable = [
        'school_id',
        'campus_id',
        'class_id',
        'stream_id',
        'student_id',
        'academic_year_id',
        'term_id',
        'date',
        'status',
        'arrival_time',
        'method',
        'late_minutes',
        'notes',
        'recorded_by',
        'synced_from_offline',
    ];

    protected $casts = [
        'date' => 'date',
        'synced_from_offline' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
