<?php

namespace QueryWatcher\Traits;

use QueryWatcher\Facades\QueryWatcher;
use Illuminate\Database\Eloquent\Builder;

trait Scopable
{
    /**
     * Register the query string params to watch
     * 
     * @param \Illuminate\Database\Eloquent\Builder  $builder
     * @param array  $keys
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeWatch(Builder $builder, $scopes)
    {
        $instance = QueryWatcher::getInstance();

        $instance->watch($builder, $scopes);

        return $builder;
    }
}
