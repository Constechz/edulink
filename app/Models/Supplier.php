<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Supplier extends Model
{
    use BelongsToSchool;

    protected $table = 'suppliers';

    protected $fillable = ['school_id', 'name', 'contact_name', 'phone', 'email', 'address'];
}
