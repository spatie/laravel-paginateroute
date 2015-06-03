<?php

namespace Spatie\PaginateRoute;

use Route;

class PaginateRoute
{
    /**
     * Register the Route::paginate macro
     * 
     * @return void
     */
    public function registerMacros()
    {
        // Unfortunately we can't do this in the service provider since routes are booted first
        app('translator')->addNamespace('paginateroute', __DIR__.'/../resources/lang');

        $pageName = trans('paginateroute::paginateroute.page');
        
        Route::macro('paginate', function ($uri, $action) use ($pageName) {
            Route::group(
                ['middleware' => 'Spatie\PaginateRoute\SetPageMiddleware'],
                function () use ($pageName, $uri, $action) {
                    Route::get($uri, $action);
                    Route::get($uri.'/'.$pageName.'/{page}', $action)->where('page', '[0-9]+');
                });
        });
    }
}
