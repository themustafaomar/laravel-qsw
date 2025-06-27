<?php

namespace Queryable\Facades;

use Illuminate\Support\Facades\Facade;

class Queryable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel.queryable';
    }
}
