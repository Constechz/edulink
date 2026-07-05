<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffQualification extends Model
{
    protected $fillable = [
        'staff_id',
        'institution',
        'qualification',
        'year_obtained',
        'certificate_path',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
