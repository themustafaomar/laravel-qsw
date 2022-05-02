<?php

namespace QueryWatcher;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\InputBag;

class QueryWatcher
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     *
     * The query instance
     */
    protected $builder;

    /**
     * @var \Symfony\Component\HttpFoundation\InputBag
     *
     * The query string parameters
     */
    protected $query;

    /**
     * @var array
     *
     * The scopes array
     */
    protected $scopes;

    /**
     * Create new instance of QueryWatcher
     *
     * @param \Symfony\Component\HttpFoundation\InputBag $query
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $scopes
     */
    public function __construct(Builder $builder, InputBag $query, array $scopes)
    {
        $this->builder = $builder;
        $this->query = $query;
        $this->scopes = $scopes;

        $this->init();
    }

    /**
     * Initialize the watch for scopes
     * 
     * @return void
     */
    protected function init()
    {
        foreach ($this->scopes as $name => $scope) {
            $this->apply($name, $scope);
        }
    }

    /**
     * Start handling the scope
     * 
     * @param string $name
     * @param string $scope
     * @return void
     */
    protected function apply($name, $scope)
    {
        if (Str::startsWith($name, 'when:')) {
            $this->executeWhen($scope, $name);
        }

        if ($this->isValidParameter($name)) {
            $this->runScope($scope, [$name]);
        }
    }

    /**
     * Check if the query parameter is valid
     * 
     * @param string $name
     * @return void
     */
    protected function isValidParameter($name)
    {
        return $this->query->has($name) && ! is_null($this->query->get($name));
    }

    /**
     * Run a scope when `when` wildcard is presented
     * 
     * @param string $scope
     * @param string $name
     * @return void
     */
    protected function executeWhen($scope, $name)
    {
        [, $params] = explode(':', $name);
        $params = explode(',', $params);

        if (Arr::has($this->query->all(), $params)) {
            $this->runScope($scope, $params);
        }
    }

    /**
     * Initalize and run the scope
     * 
     * @param string $scope
     * @param array $params
     * @return void
     */
    protected function runScope($scope, $params)
    {
        $instance = new $scope();

        $this->appendKeys(
            $instance, $this->getParameters($params)
        );

        $instance->apply($this->builder, $this->builder->getModel());
    }

    /**
     * Get params from query string
     * 
     * @param array $params
     * @return array
     */
    protected function getParameters($params)
    {
        return Arr::only($this->query->all(), $params);
    }

    /**
     * Set matched query string params as props
     * 
     * @param \QueryWatcher\Contracts\Scope $instance
     * @param array $data
     * @return void
     */
    protected function appendKeys($instance, $data)
    {
        foreach ($data as $name => $value) {
            $instance->{$name} = $value;
        }
    }
}
