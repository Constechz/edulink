<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class TransportFee extends Model
{
    use BelongsToSchool;

    protected $table = 'transport_fees';

    protected $fillable = ['school_id', 'route_id', 'student_id', 'academic_year_id', 'term_id', 'amount'];
}
