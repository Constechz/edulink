<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class AiRecommendation extends Model
{
    use BelongsToSchool;

    protected $table = 'ai_recommendations';

    protected $fillable = ['school_id', 'student_id', 'recommendation_text', 'status', 'reviewed_by', 'reviewed_at'];
}
