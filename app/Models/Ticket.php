<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Ticket extends Model
{
    use BelongsToSchool;

    protected $table = 'tickets';

    protected $fillable = ['school_id', 'user_id', 'category_id', 'subject', 'body', 'priority', 'status', 'assigned_agent_id'];
}
