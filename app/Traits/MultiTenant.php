<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait MultiTenant {

    public static function bootMultiTenant() {

        if (auth()->check()) {

            static::saving(function ($model) {
                $model->company_id = company_id();
            });

            static::addGlobalScope('company_id', function (Builder $builder) {
                if (auth()->user()->user_type != 'admin') {
                    return $builder->where('company_id', company_id());
                }
            });

        }
        
    }

}