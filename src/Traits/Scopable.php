<?php

namespace Queryable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Queryable\Facades\Queryable;

trait Scopable
{
    /**
     * Register the query string params to watch.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array  $keys
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWatch(Builder $builder, $scopes)
    {
        $instance = Queryable::getInstance();

        $instance->watch($builder, $scopes);

        return $builder;
    }
}
