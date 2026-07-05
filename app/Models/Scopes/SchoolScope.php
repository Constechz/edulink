<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->bound('tenant') && app('tenant') !== null) {
            $table = $model->getTable();
            if ($table === 'roles') {
                $builder->where(function ($query) use ($table) {
                    $query->where($table . '.school_id', app('tenant')->id)
                          ->orWhereNull($table . '.school_id');
                });
            } else {
                $builder->where($table . '.school_id', app('tenant')->id);
            }
        }
    }
}
