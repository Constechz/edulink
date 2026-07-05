<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id',
        'campus_id',
        'name',
        'code',
        'hod_user_id',
        'description',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function hod()
    {
        return $this->belongsTo(User::class, 'hod_user_id');
    }

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
