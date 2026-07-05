<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class HealthVisit extends Model
{
    use BelongsToSchool;

    protected $table = 'health_visits';

    protected $fillable = ['school_id', 'student_id', 'visit_date', 'symptoms', 'diagnosis', 'treatment', 'medication_given', 'notes', 'recorded_by'];
}
