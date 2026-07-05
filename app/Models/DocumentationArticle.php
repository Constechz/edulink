<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentationArticle extends Model
{
    protected $table = 'documentation_articles';

    protected $fillable = [
        'portal',
        'category',
        'title',
        'slug',
        'content',
        'is_published',
        'display_order'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'display_order' => 'integer'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = static::generateUniqueSlug($article->title, $article->id);
            }
        });
    }

    /**
     * Generate a unique slug for the article.
     */
    protected static function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
