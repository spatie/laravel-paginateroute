<?php

namespace Spatie\PaginateRoute;

use Closure;
use Illuminate\Pagination\Paginator;
use Route;

class SetPageMiddleware
{
    /**
     * Set the current page based on the page route parameter before the route's action is executed.
     *
     * @return \Illuminate\Http\Request
     */
    public function handle($request, Closure $next)
    {
        $page = Route::getCurrentRoute()->parameter('page', 1);

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        return $next($request);
    }
}
