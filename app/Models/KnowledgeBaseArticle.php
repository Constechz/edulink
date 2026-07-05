<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseArticle extends Model
{
    protected $table = 'knowledge_base_articles';

    protected $fillable = ['title', 'slug', 'content', 'target_role', 'is_published'];
}
