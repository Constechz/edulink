<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Asset extends Model
{
    use BelongsToSchool;

    protected $table = 'assets';

    protected $fillable = ['school_id', 'name', 'barcode', 'serial_number', 'purchase_date', 'purchase_cost', 'depreciation_rate', 'status'];
}
