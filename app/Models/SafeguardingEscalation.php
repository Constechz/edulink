<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafeguardingEscalation extends Model
{
    protected $table = 'safeguarding_escalations';

    protected $fillable = ['case_id', 'agency_name', 'escalated_date', 'officer_in_charge', 'action_details'];
}
