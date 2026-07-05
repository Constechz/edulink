<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentScore extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'student_scores';

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'stream_id',
        'subject_id',
        'term_id',
        'academic_year_id',
        'scoring_configuration_id',
        'teacher_id',
        'component_scores',
        'raw_class_total',
        'scaled_class_score',
        'raw_exam_score',
        'scaled_exam_score',
        'grand_total',
        'grade',
        'grade_point',
        'subject_position',
        'total_students',
        'remarks',
        'is_absent_exam',
        'moderation_note',
        'status',
        'submitted_at',
        'hod_verified_at',
        'hod_verified_by',
        'approved_at',
        'approved_by',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'component_scores' => 'json',
        'raw_class_total' => 'decimal:2',
        'scaled_class_score' => 'decimal:2',
        'raw_exam_score' => 'decimal:2',
        'scaled_exam_score' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_absent_exam' => 'boolean',
        'submitted_at' => 'datetime',
        'hod_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
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

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function configuration()
    {
        return $this->belongsTo(ScoringConfiguration::class, 'scoring_configuration_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
