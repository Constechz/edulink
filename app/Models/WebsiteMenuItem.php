<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteMenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'url',
        'page_id',
        'open_new_tab',
        'display_order',
    ];

    protected $casts = [
        'open_new_tab' => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(WebsiteMenu::class);
    }

    public function parent()
    {
        return $this->belongsTo(WebsiteMenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(WebsiteMenuItem::class, 'parent_id')->orderBy('display_order');
    }

    public function page()
    {
        return $this->belongsTo(WebsitePage::class);
    }
}
