<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class StockTransaction extends Model
{
    use BelongsToSchool;

    protected $table = 'stock_transactions';

    protected $fillable = ['school_id', 'inventory_item_id', 'type', 'quantity', 'reference_type', 'reference_id', 'transaction_date', 'recorded_by', 'notes'];
}
