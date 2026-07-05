<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    protected $table = 'asset_assignments';

    protected $fillable = ['asset_id', 'assigned_to', 'assigned_by', 'assigned_date', 'returned_date'];
}
