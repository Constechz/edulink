<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class StudentPromotionRecord extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'promotion_run_id',
        'student_id',
        'from_class_id',
        'to_class_id',
        'academic_year_id',
        'term1_score',
        'term2_score',
        'term3_score',
        'computed_average',
        'method_used',
        'rule_snapshot_json',
        'decision',
        'is_override',
        'override_reason',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'term1_score' => 'decimal:2',
        'term2_score' => 'decimal:2',
        'term3_score' => 'decimal:2',
        'computed_average' => 'decimal:2',
        'rule_snapshot_json' => 'json',
        'is_override' => 'boolean',
        'decided_at' => 'datetime',
    ];

    public function promotionRun()
    {
        return $this->belongsTo(PromotionRun::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromClass()
    {
        return $this->belongsTo(SchoolClass::class, 'from_class_id');
    }

    public function toClass()
    {
        return $this->belongsTo(SchoolClass::class, 'to_class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
