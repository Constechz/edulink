<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class TicketCategory extends Model
{
    use BelongsToSchool;

    protected $table = 'ticket_categories';

    protected $fillable = ['school_id', 'name'];
}
