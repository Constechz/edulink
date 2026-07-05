<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class SmsCreditLedger extends Model
{
    use BelongsToSchool;

    protected $table = 'sms_credit_ledger';

    public $timestamps = false;

    protected $fillable = [
        'school_id',
        'type',
        'credits',
        'balance_after',
        'reference',
        'note',
        'created_at',
    ];

    protected $casts = [
        'credits' => 'integer',
        'balance_after' => 'integer',
        'created_at' => 'datetime',
    ];
}
