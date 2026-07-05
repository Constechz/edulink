<?php

namespace App\Models\Traits;

use App\Models\Scopes\SchoolScope;
use App\Models\School;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(new SchoolScope);

        static::creating(function ($model) {
            if (app()->bound('tenant') && app('tenant') !== null) {
                $model->school_id = $model->school_id ?? app('tenant')->id;
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
