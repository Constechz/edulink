<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Webhook extends Model
{
    use BelongsToSchool;

    protected $table = 'webhooks';

    protected $fillable = ['school_id', 'name', 'url', 'secret', 'subscribed_events', 'is_active'];

    protected $casts = [
        'subscribed_events' => 'array',
        'is_active' => 'boolean',
    ];
}
