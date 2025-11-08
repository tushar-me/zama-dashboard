<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StoreScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $store = request()->user()->store;
        if (! $store) {
            $builder->whereRaw('0 = 1');
            return;
        }
        $builder->where(function ($query) use ($store) {
            $query->where('store_id', $store->id);
        });
    }
}
