<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class StaffDeduction extends Model
{
    use BelongsToSchool;

    protected $table = 'staff_deductions';

    protected $fillable = ['school_id', 'staff_id', 'name', 'amount', 'is_recurring'];
}
