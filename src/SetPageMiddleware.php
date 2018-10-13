<?php

namespace Spatie\PaginateRoute;

use Route;
use Closure;
use View;
use Illuminate\Pagination\Paginator;

class SetPageMiddleware
{
    /**
     * Set the current page based on the page route parameter before the route's action is executed.
     *
     * @return \Illuminate\Http\Request
     */
    public function handle($request, Closure $next)
    {
        Paginator::currentPageResolver(function () {
            return app('paginateroute')->currentPage();
        });
        
        View::share('hasPaginateRoute', true);

        return $next($request);
    }
}
