<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'plan_id',
        'description',
        'starts_at',
        'ends_at',
        'amount_paid',
        'currency',
        'payment_reference',
        'payment_method',
        'status',
        'auto_renew',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
