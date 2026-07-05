<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'level',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(GradingScaleItem::class)->orderBy('display_order');
    }
}
