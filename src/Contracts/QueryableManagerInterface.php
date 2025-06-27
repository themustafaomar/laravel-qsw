<?php

namespace Queryable\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface QueryableManagerInterface
{
    /**
     * Apply scopes on a builder
     *
     * @return \Queryable\Queryable
     */
    public function watch(Builder $builder, array $scopes);

    /**
     * Get an instance of this class
     *
     * @return self
     */
    public function getInstance();
}
