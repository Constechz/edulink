<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class IncidentReport extends Model
{
    use BelongsToSchool;

    protected $table = 'incident_reports';

    protected $fillable = ['school_id', 'reporter_name', 'reporter_phone', 'incident_type', 'description', 'date_reported'];
}
