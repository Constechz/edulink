<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DormitoryRoom extends Model
{
    protected $table = 'dormitory_rooms';

    protected $fillable = ['dormitory_id', 'room_number', 'capacity'];

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function beds()
    {
        return $this->hasMany(DormitoryBed::class, 'room_id');
    }
}

