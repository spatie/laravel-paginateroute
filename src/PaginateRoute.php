<?php

namespace Spatie\PaginateRoute;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Translation\Translator;
use Spatie\String\String;

class PaginateRoute
{
    /**
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @param  \Illuminate\Translation\Translator $translator
     * @param  \Illuminate\Routing\Router $router
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    public function __construct(Translator $translator, Router $router, Request $request, UrlGenerator $urlGenerator)
    {
        $this->translator    = $translator;
        $this->router        = $router;
        $this->request       = $request;
        $this->urlGenerator  = $urlGenerator;

        // Unfortunately we can't do this in the service provider since routes are booted first
        $this->translator->addNamespace('paginateroute', __DIR__.'/../resources/lang');

        $this->pageName = $this->translator->get('paginateroute::paginateroute.page');
    }

    /**
     * Register the Route::paginate macro
     * 
     * @return void
     */
    public function registerMacros()
    {
        $pageName = $this->pageName;
        $router   = $this->router;

        $router->macro('paginate', function ($uri, $action) use ($pageName, $router) {
            $router->group(
                ['middleware' => 'Spatie\PaginateRoute\SetPageMiddleware'],
                function () use ($pageName, $router, $uri, $action) {
                    $router->get($uri, $action);
                    $router->get($uri.'/'.$pageName.'/{page}', $action)->where('page', '[0-9]+');
                });
        });
    }

    /**
     * Get the next page number
     * 
     * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
     * @return string|null
     */
    public function nextPage(Paginator $paginator)
    {
        if (!$paginator->hasMorePages()) {
            return null;
        }

        return $this->router->getCurrentRoute()->parameter('page') + 1;
    }

    /**
     * Determine wether there is a next page
     * 
     * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
     * @return bool
     */
    public function hasNextPage(Paginator $paginator)
    {
        return $this->nextPage($paginator) !== null;
    }

    /**
     * Get the next page url
     * 
     * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
     * @return string|null
     */
    public function nextPageUrl(Paginator $paginator)
    {
        $nextPage = $this->nextPage($paginator);

        if ($nextPage === null) {
            return $nextPage;
        }

        // This should call the current action with a different parameter
        // Afaik there's no cleaner way to do this
        
        $currentPageUrl = new String($this->router->getCurrentRoute()->getUri());
        $nextPageUrl = $currentPageUrl->replaceLast('{page}', $nextPage);

        return $this->urlGenerator->to($nextPageUrl);
    }

    /**
     * Get the previous page number
     * 
     * @return string|null
     */
    public function previousPage()
    {
        if ($this->router->getCurrentRoute()->parameter('page') <= 1) {
            return null;
        }

        return $this->router->getCurrentRoute()->parameter('page') - 1;
    }

    /**
     * Determine wether there is a previous page
     * 
     * @return bool
     */
    public function hasPreviousPage()
    {
        return $this->previousPage() !== null;
    }

    /**
     * Get the previous page url
     * 
     * @param  bool $full  Return the full version of the url in for the first page
     *                     Ex. /users/page/1 instead of /users
     * @return string|null
     */
    public function previousPageUrl($full = false)
    {
        $previousPage = $this->previousPage();

        if ($previousPage === null) {
            return null;
        }

        // This should call the current action with a different parameter
        // Afaik there's no cleaner way to do this
        
        $currentPageUrl = new String($this->router->getCurrentRoute()->getUri());

        if ($previousPage === 1 && !$full) {
            $previousPageUrl = $currentPageUrl->replaceLast($this->pageName.'/{page}', '');
        } else {
            $previousPageUrl = $currentPageUrl->replaceLast('{page}', $previousPage);
        }

        return $this->urlGenerator->to($previousPageUrl);
    }
}
