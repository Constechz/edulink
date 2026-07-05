<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class PurchaseOrder extends Model
{
    use BelongsToSchool;

    protected $table = 'purchase_orders';

    protected $fillable = ['school_id', 'supplier_id', 'order_number', 'order_date', 'total_amount', 'status'];
}
