<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class DisciplineCase extends Model
{
    use BelongsToSchool;

    protected $table = 'discipline_cases';

    protected $fillable = ['school_id', 'student_id', 'reported_by', 'incident_date', 'category', 'description', 'status'];
}
