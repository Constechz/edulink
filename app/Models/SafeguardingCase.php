<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class SafeguardingCase extends Model
{
    use BelongsToSchool;

    protected $table = 'safeguarding_cases';

    protected $fillable = ['school_id', 'student_id', 'reported_by', 'incident_date', 'incident_type', 'details', 'status'];
}
