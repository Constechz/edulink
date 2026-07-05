<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class HostelRollcall extends Model
{
    use BelongsToSchool;

    protected $table = 'hostel_rollcalls';

    protected $fillable = ['school_id', 'dormitory_id', 'rollcall_date', 'recorded_by', 'notes'];
}
