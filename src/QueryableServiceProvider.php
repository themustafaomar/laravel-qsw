<?php

namespace Queryable;

use Illuminate\Support\ServiceProvider;
use Queryable\Commands\ScopeMakeCommand;

class QueryableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravel.queryable', function ($app) {
            return new QueryableManager($app->make('request')->query);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if ($this->app->runningInConsole()) {
            // $this->commands([
            //     ScopeMakeCommand::class,
            // ]);
        // }
    }
}
