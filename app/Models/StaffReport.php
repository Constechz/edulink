<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class StaffReport extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'staff_id',
        'reported_by',
        'category',
        'severity',
        'description',
        'status',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
