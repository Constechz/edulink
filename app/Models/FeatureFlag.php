<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'feature_key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];
}
