<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class InventoryItem extends Model
{
    use BelongsToSchool;

    protected $table = 'inventory_items';

    protected $fillable = ['school_id', 'category_id', 'name', 'code', 'description', 'unit_of_measure', 'quantity_in_stock', 'reorder_level'];

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }
}
