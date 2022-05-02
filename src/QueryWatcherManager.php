<?php

namespace QueryWatcher;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\InputBag;
use QueryWatcher\Contracts\QueryWatcherManagerInterface;

class QueryWatcherManager implements QueryWatcherManagerInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\InputBag
     *
     * The query instance
     */
    protected $query;

    /**
     * Create new instance of QueryWatcher
     *
     * @param \Symfony\Component\HttpFoundation\InputBag $query
     */
    public function __construct(InputBag $query)
    {
        $this->query = $query;
    }

    /**
     * Apply scopes on a builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $scopes
     * @return \QueryWatcher\QueryWatcher
     */
    public function watch(Builder $builder, array $scopes)
    {
        return new QueryWatcher($builder, $this->query, $scopes);
    }

    /**
     * Get an instance of this class
     *
     * @return this
     */
    public function getInstance()
    {
        return $this;
    }
}
