<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DormitoryBed extends Model
{
    protected $table = 'dormitory_beds';

    protected $fillable = ['room_id', 'bed_number', 'is_occupied'];

    public function room()
    {
        return $this->belongsTo(DormitoryRoom::class, 'room_id');
    }
}

