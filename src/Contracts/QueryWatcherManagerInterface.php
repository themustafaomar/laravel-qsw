<?php

namespace QueryWatcher\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface QueryWatcherManagerInterface
{
    /**
     * Apply scopes on a builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $scopes
     * @return \QueryWatcher\QueryWatcher
     */
    public function watch(Builder $builder, array $scopes);

    /**
     * Get an instance of this class
     * 
     * @return this
     */
    public function getInstance();
}
