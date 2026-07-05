<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScoreComponent extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id',
        'scoring_configuration_id',
        'name',
        'max_marks',
        'display_order',
        'is_active',
        'is_required',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'max_marks' => 'decimal:2',
        'is_active' => 'boolean',
        'is_required' => 'boolean',
    ];

    public function configuration()
    {
        return $this->belongsTo(ScoringConfiguration::class, 'scoring_configuration_id');
    }
}
