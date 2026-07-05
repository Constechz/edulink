<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAppraisal extends Model
{
    protected $fillable = [
        'staff_id',
        'academic_year_id',
        'term_id',
        'appraiser_id',
        'criteria',
        'total_score',
        'grade',
        'comments',
        'status',
    ];

    protected $casts = [
        'criteria' => 'json',
        'total_score' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function appraiser()
    {
        return $this->belongsTo(User::class, 'appraiser_id');
    }
}
