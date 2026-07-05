<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class HealthRecord extends Model
{
    use BelongsToSchool;

    protected $table = 'health_records';

    protected $fillable = ['school_id', 'student_id', 'blood_group', 'allergies', 'medical_conditions'];
}
