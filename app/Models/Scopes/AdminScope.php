<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AdminScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $admin = auth('admin')->user();
        if (! $admin) {
            $builder->whereRaw('0 = 1');
            return;
        }

        if (! in_array($admin->role, config('app.admin_full_access_roles'))) {
            $builder->where(function ($query) use ($admin) {
                $query->where('created_by', $admin->id)
                      ->orWhere('last_updated_by', $admin->id);
            });
        }
    }
}
