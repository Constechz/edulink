<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScoringConfiguration extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id',
        'campus_id',
        'level',
        'subject_id',
        'academic_year_id',
        'name',
        'class_score_max',
        'class_score_weight',
        'exam_score_max',
        'exam_score_weight',
        'grand_total',
        'rounding_method',
        'decimal_places',
        'is_active',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'class_score_max' => 'decimal:2',
        'class_score_weight' => 'decimal:2',
        'exam_score_max' => 'decimal:2',
        'exam_score_weight' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function components()
    {
        return $this->hasMany(ScoreComponent::class)->orderBy('display_order');
    }
}
