<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiFlagType extends Model
{
    protected $table = 'ai_flag_types';

    protected $fillable = ['name', 'slug', 'description'];
}
