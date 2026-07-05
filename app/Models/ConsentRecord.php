<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class ConsentRecord extends Model
{
    use BelongsToSchool;

    protected $table = 'consent_records';

    protected $fillable = ['school_id', 'student_id', 'consent_type', 'is_granted', 'recorded_by_guardian_id', 'recorded_at'];
}
