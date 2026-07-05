<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class HostelIncident extends Model
{
    use BelongsToSchool;

    protected $table = 'hostel_incidents';

    protected $fillable = ['school_id', 'student_id', 'reported_by', 'incident_date', 'description', 'action_taken'];
}
