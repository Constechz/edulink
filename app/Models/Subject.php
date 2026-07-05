<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id',
        'department_id',
        'name',
        'code',
        'level',
        'is_core',
        'is_elective',
    ];

    protected $casts = [
        'is_core' => 'boolean',
        'is_elective' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
