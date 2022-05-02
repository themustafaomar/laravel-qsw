<?php

namespace QueryWatcher\Facades;

use Illuminate\Support\Facades\Facade;

class QueryWatcher extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel.qsw';
    }
}
