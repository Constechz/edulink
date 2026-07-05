<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class LeaveRequest extends Model
{
    use BelongsToSchool;

    protected $table = 'leave_requests';

    protected $fillable = ['school_id', 'staff_id', 'leave_type_id', 'start_date', 'end_date', 'status', 'reason', 'approved_by'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
