<?php

namespace QueryWatcher;

use Illuminate\Support\ServiceProvider;
use QueryWatcher\Commands\ScopeMakeCommand;

class QueryWatcherServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravel.qsw', function ($app) {
            return new QueryWatcherManager($app->make('request')->query);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ScopeMakeCommand::class,
            ]);
        }
    }
}
