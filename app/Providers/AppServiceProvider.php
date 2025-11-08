<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Builder::macro('search', function ($attributes, $searchTerm) {
            return $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        ! ($attribute instanceof \Illuminate\Contracts\Database\Query\Expression) && str_contains((string) $attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $attrs = explode('.', (string) $attribute);
                            $relatedAttribute = array_pop($attrs);
                            $relation = implode('.', $attrs);
                            $query->orWhereRelation($relation, $relatedAttribute, 'LIKE', "%{$searchTerm}%");
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            })
            ->paginate(request()->per_page ?? 50);
        });
    }
}
