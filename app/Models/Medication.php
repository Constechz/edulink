<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Medication extends Model
{
    use BelongsToSchool;

    protected $table = 'medications';

    protected $fillable = ['school_id', 'name', 'quantity_in_stock', 'reorder_level'];
}
