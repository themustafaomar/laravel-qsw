<?php

namespace Queryable;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\InputBag;

class Queryable
{
    /**
     * Create new instance of Queryable
     */
    public function __construct(
        private Builder $builder, private InputBag $query, private array|string $scopes
    ) {
        $this->init();
    }

    /**
     * Initialize the watch for scopes
     *
     * @return void
     */
    protected function init()
    {
        // If the scopes is a string, we'll assume it's a queryable
        // instance and we will run the queryable instance directly,
        // this is another way for defining scopes via an isolated class.
        if (is_string($this->scopes)) {
            return $this->runQueryable();
        }

        foreach ($this->scopes as $name => $scope) {
            [$key, $default, $namespace] = $this->normalize($scope);

            $this->apply(
                $key, $namespace, $default
            );
        }
    }

    /**
     * Run the queryable methods
     *
     * @return void
     */
    private function runQueryable()
    {
        $queryable = new $this->scopes($this->builder);

        if (! method_exists($queryable, 'queryables')) {
            throw new Exception(
                "The 'queryables' method is missing. Define it to specify the allowed query parameters."
            );
        }

        // Here we will loop through all queryables and run the
        // corresponding queryable method this allows us to dynamically
        // apply scopes based on the query parameters provided in the request.
        foreach ($queryable->queryables() as $scope) {
            [$key, $default, $namespace] = $this->normalize($scope);

            if ($namespace) {
                $this->apply($key, $namespace, $default);
            } else {
                if (! $default && ! $this->valid($key)) {
                    continue;
                }

                $queryable->{$key}(
                    $this->query->get($key) ?? $default
                );
            }
        }
    }

    /**
     * Start handling the scope
     *
     * @param  string  $name
     * @param  string|array  $scope
     * @return void
     */
    protected function apply($name, $scope, $default)
    {
        if ($default === null && ! $this->query->has($name)) {
            return;
        }

        if (! class_exists($scope)) {
            throw new Exception("Scope [$scope] does not exist.");
        }

        $instance = new $scope;
        $instance->value = $this->query->get($name) ?? $default;

        $instance->apply(
            $this->builder,
            $this->builder->getModel()
        );
    }

    /**
     * Check if the query parameter is valid
     *
     * @param  string  $name
     * @return bool
     */
    private function valid($name)
    {
        return $this->query->has($name) && $this->query->get($name) !== null;
    }

    /**
     * Initalize and run the scope
     *
     * @param  string  $scope
     * @param  array  $params
     * @return void
     */
    protected function runScope($scope, $params)
    {
        $instance = new $scope;

        foreach ($this->getParameters($params) as $name => $value) {
            $instance->value = $value ?? $default;
        }

        $instance->apply($this->builder, $this->builder->getModel());
    }

    /**
     * Normalize the scope options.
     * 
     * @param  mixed  $scope
     * @return array<string|null>
     */
    private function normalize($scope)
    {
        $key = $default = $namespace = null;
        $parts = explode(':', $scope, 2);
        $key = $parts[0];

        if (isset($parts[1])) {
            $rest = $parts[1];

            if (str_contains($rest, ';')) {
                [$default, $namespace] = array_pad(
                    explode(';', $rest, 2), 2, null
                );
            } elseif (str_contains($rest, ',')) {
                $lastComma = strrpos($rest, ',');
                $default = substr($rest, 0, $lastComma);
                $namespace = substr($rest, $lastComma + 1);
            } else {
                $default = $rest;
            }
        } elseif (str_contains($key, ',')) {
            [$key, $namespace] = array_pad(
                explode(',', $key, 2), 2, null
            );
        }

        return [$key, $default, $namespace];
    }

    /**
     * Get params from query string
     *
     * @param  array  $params
     * @return array
     */
    protected function getParameters($params)
    {
        return Arr::only($this->query->all(), $params);
    }
}
