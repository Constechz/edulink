<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageRevision extends Model
{
    protected $fillable = [
        'website_page_id',
        'revision_number',
        'html_content',
        'css_content',
        'components_json',
        'is_current_draft',
        'is_published',
        'published_at',
        'published_by',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_current_draft' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function websitePage()
    {
        return $this->belongsTo(WebsitePage::class);
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
