<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Immunization extends Model
{
    use BelongsToSchool;

    protected $table = 'immunizations';

    protected $fillable = ['school_id', 'student_id', 'vaccine_name', 'date_administered', 'notes'];
}
