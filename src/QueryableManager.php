<?php

namespace Queryable;

use Illuminate\Database\Eloquent\Builder;
use Queryable\Contracts\QueryableManagerInterface;
use Symfony\Component\HttpFoundation\InputBag;

class QueryableManager implements QueryableManagerInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\InputBag
     *
     * The query instance
     */
    protected $query;

    /**
     * Create new instance.
     * 
     * @param  \Symfony\Component\HttpFoundation\InputBag  $query
     */
    public function __construct(InputBag $query)
    {
        $this->query = $query;
    }

    /**
     * Apply scopes on a builder
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array $scopes
     * @return \Queryable\Queryable
     */
    public function watch(Builder $builder, array|string $scopes)
    {
        return new Queryable($builder, $this->query, $scopes);
    }

    /**
     * Get an instance of this class
     *
     * @return self
     */
    public function getInstance()
    {
        return $this;
    }
}
