<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Requisition extends Model
{
    use BelongsToSchool;

    protected $table = 'requisitions';

    protected $fillable = ['school_id', 'requested_by', 'inventory_item_id', 'quantity', 'status', 'notes'];
}
