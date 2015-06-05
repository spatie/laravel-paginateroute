<?php

namespace Spatie\PaginateRoute;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class PaginateRouteServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/lang' => base_path('resources/lang/vendor/paginateroute'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('paginateroute', 'Spatie\PaginateRoute\PaginateRoute');
    }
}
