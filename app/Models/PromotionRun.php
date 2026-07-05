<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class PromotionRun extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'level',
        'run_by',
        'status',
        'generated_at',
        'approved_by',
        'approved_at',
        'published_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function runner()
    {
        return $this->belongsTo(User::class, 'run_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function records()
    {
        return $this->hasMany(StudentPromotionRecord::class);
    }
}
