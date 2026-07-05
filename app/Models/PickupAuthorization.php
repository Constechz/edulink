<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class PickupAuthorization extends Model
{
    use BelongsToSchool;

    protected $table = 'pickup_authorizations';

    protected $fillable = ['school_id', 'student_id', 'authorized_name', 'phone_number', 'relationship', 'photo'];
}
