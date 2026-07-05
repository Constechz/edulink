<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Dormitory extends Model
{
    use BelongsToSchool;

    protected $table = 'dormitories';

    protected $fillable = ['school_id', 'name', 'gender_allowed', 'warden_user_id', 'capacity'];

    public function rooms()
    {
        return $this->hasMany(DormitoryRoom::class, 'dormitory_id');
    }
}

