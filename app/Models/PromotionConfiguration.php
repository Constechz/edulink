<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class PromotionConfiguration extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'level',
        'class_id',
        'method',
        'term_weights_json',
        'promotion_threshold',
        'conditional_threshold',
        'min_subjects_to_pass',
        'per_subject_pass_mark',
        'repeat_limit',
        'exclude_terminal_year',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'term_weights_json' => 'json',
        'promotion_threshold' => 'decimal:2',
        'conditional_threshold' => 'decimal:2',
        'per_subject_pass_mark' => 'decimal:2',
        'exclude_terminal_year' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
