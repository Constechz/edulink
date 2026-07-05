<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoItem extends Model
{
    protected $table = 'po_items';

    protected $fillable = ['purchase_order_id', 'inventory_item_id', 'quantity', 'unit_price', 'total_price'];
}
