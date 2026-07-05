<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorBadge extends Model
{
    protected $table = 'visitor_badges';

    protected $fillable = ['visitor_log_id', 'badge_number', 'is_returned', 'returned_at'];
}
