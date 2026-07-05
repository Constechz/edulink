<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class AlumniDonation extends Model
{
    use BelongsToSchool;

    protected $table = 'alumni_donations';

    protected $fillable = ['school_id', 'alumni_profile_id', 'amount', 'donation_date', 'purpose', 'payment_method', 'payment_reference'];
}
