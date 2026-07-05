<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class InventoryCategory extends Model
{
    use BelongsToSchool;

    protected $table = 'inventory_categories';

    protected $fillable = ['school_id', 'name', 'description'];
}
