<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class HostelFee extends Model
{
    use BelongsToSchool;

    protected $table = 'hostel_fees';

    protected $fillable = ['school_id', 'dormitory_id', 'amount', 'due_date'];
}
