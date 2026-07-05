<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Allergy extends Model
{
    use BelongsToSchool;

    protected $table = 'allergies';

    protected $fillable = ['school_id', 'student_id', 'allergen', 'severity', 'notes'];
}
