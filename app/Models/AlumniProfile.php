<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class AlumniProfile extends Model
{
    use BelongsToSchool;

    protected $table = 'alumni_profiles';

    protected $fillable = ['school_id', 'student_id', 'graduation_year', 'current_occupation', 'employer', 'higher_institution', 'linkedin_url'];
}
