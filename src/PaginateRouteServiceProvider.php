<?php

namespace Spatie\PaginateRoute;

use Illuminate\Support\ServiceProvider;

class PaginateRouteServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/lang' => base_path('resources/lang/vendor/paginateroute'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('paginateroute', 'Spatie\PaginateRoute\PaginateRoute');
    }
}
