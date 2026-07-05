<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'department_id',
        'name',
        'code',
        'duration_years',
        'level',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'programme_id');
    }
}
