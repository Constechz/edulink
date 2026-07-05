<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'name',
        'description',
        'status',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }
}
