<?php

/**
 * MIT License
 * 
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * See LICENSE file in the project root for full license information.
 * 
 * GitHub: https://github.com/ffrz
 * Email: fahmifauzirahman@gmail.com
 */

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;

class BaseModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();


        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::id()) {
                $model = $builder->getModel();
                $builder->where($model->getTable() . '.user_id', Auth::id());
            }
        });

        static::creating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'created_datetime')) {
                if (Auth::id()) {
                    $model->user_id = Auth::id();
                    $model->created_datetime = current_datetime();
                    $model->created_by_uid = Auth::id();
                }
            }
            return true;
        });

        static::updating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'updated_datetime')) {
                $model->updated_datetime = current_datetime();
                $model->updated_by_uid = Auth::id();
            }
            return true;
        });
    }
}
