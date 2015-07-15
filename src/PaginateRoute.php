<?php

namespace Spatie\PaginateRoute;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Translation\Translator;

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
     * @var string
     */
    protected $pageName;

    /**
     * @param \Illuminate\Translation\Translator         $translator
     * @param \Illuminate\Routing\Router                 $router
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    public function __construct(Translator $translator, Router $router, Request $request, UrlGenerator $urlGenerator)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->request = $request;
        $this->urlGenerator = $urlGenerator;

        // Unfortunately we can't do this in the service provider since routes are booted first
        $this->translator->addNamespace('paginateroute', __DIR__.'/../resources/lang');

        $this->pageName = $this->translator->get('paginateroute::paginateroute.page');
    }

    /**
     * Register the Route::paginate macro.
     */
    public function registerMacros()
    {
        $pageName = $this->pageName;
        $router = $this->router;

        $router->macro('paginate', function ($uri, $action) use ($pageName, $router) {
            $router->group(
                ['middleware' => 'Spatie\PaginateRoute\SetPageMiddleware'],
                function () use ($pageName, $router, $uri, $action) {
                    $router->get($uri.'/'.$pageName.'/{page}', $action)->where('page', '[0-9]+');
                    $router->get($uri, $action);
                });
        });
    }

    /**
     * Get the next page number.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator $paginator
     *
     * @return string|null
     */
    public function nextPage(Paginator $paginator)
    {
        if (!$paginator->hasMorePages()) {
            return;
        }

        return $this->router->getCurrentRoute()->parameter('page', 1) + 1;
    }

    /**
     * Determine wether there is a next page.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator $paginator
     *
     * @return bool
     */
    public function hasNextPage(Paginator $paginator)
    {
        return $this->nextPage($paginator) !== null;
    }

    /**
     * Get the next page url.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator $paginator
     *
     * @return string|null
     */
    public function nextPageUrl(Paginator $paginator)
    {
        $nextPage = $this->nextPage($paginator);

        if ($nextPage === null) {
            return;
        }

        return $this->generatePageUrl($nextPage);
    }

    /**
     * Get the previous page number.
     *
     * @return string|null
     */
    public function previousPage()
    {
        if ($this->router->getCurrentRoute()->parameter('page') <= 1) {
            return;
        }

        return $this->router->getCurrentRoute()->parameter('page') - 1;
    }

    /**
     * Determine wether there is a previous page.
     *
     * @return bool
     */
    public function hasPreviousPage()
    {
        return $this->previousPage() !== null;
    }

    /**
     * Get the previous page url.
     *
     * @param bool $full Return the full version of the url in for the first page
     *                   Ex. /users/page/1 instead of /users
     *
     * @return string|null
     */
    public function previousPageUrl($full = false)
    {
        $previousPage = $this->previousPage();

        if ($previousPage === null) {
            return;
        }

        return $this->generatePageUrl($previousPage, $full);
    }

    /**
     * Get all urls in an array.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param bool $full Return the full version of the url in for the first page
     *                   Ex. /users/page/1 instead of /users
     *
     * @return array
     */
    public function allUrls(LengthAwarePaginator $paginator, $full = false)
    {
        if (!$paginator->hasPages()) {
            return [];
        }

        $urls = [];

        for ($page = 1; $page <= $paginator->lastPage(); ++$page) {
            $urls[] = $this->generatePageUrl($page, $full);
        }

        return $urls;
    }

    /**
     * Render a plain html list with all urls. The current page gets a current class on the list item.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param bool $full Return the full version of the url in for the first page
     *                   Ex. /users/page/1 instead of /users
     *
     * @return string
     */
    public function renderHtml(LengthAwarePaginator $paginator, $full = false)
    {
        $urls = $this->allUrls($paginator, $full);

        $current = (int) $this->router->getCurrentRoute()->getParameter('page') ?: 1;

        $listItems = '<ul>';

        foreach ($urls as $i => $url) {
            if ($i + 1 === $current) {
                $listItems .= '<li class="active">';
            } else {
                $listItems .= '<li>';
            }

            $listItems .= '<a href="'.$url.'">'.($i + 1).'</a></li>';
        }

        $listItems .= '</ul>';

        return $listItems;
    }

    /**
     * Generate a page url, based on the request's current url.
     *
     * @param int  $page
     * @param bool $full Return the full version of the url in for the first page
     *                   Ex. /users/page/1 instead of /users
     *
     * @return string
     */
    public function generatePageUrl($page, $full = false)
    {
        $currentPageUrl = $this->router->getCurrentRoute()->getUri();

        // If the page key isn't in the url, we're on the index page
        if ($this->getUrlSegment($currentPageUrl, -2) !== $this->pageName) {
            $baseUrl = trim($currentPageUrl, '/');
        } else {
            $baseUrl = trim(str_replace($this->pageName.'/{page}', '', $currentPageUrl), '/');
        }

        if ($page === 1 && !$full) {
            $newPageUrl = $baseUrl;
        } else {
            $newPageUrl = $baseUrl.'/'.$this->pageName.'/'.$page;
        }

        return $this->urlGenerator->to($newPageUrl);
    }

    /**
     * @param string $uri
     * @param int    $index
     *
     * @return string
     */
    protected function getUrlSegment($uri, $index)
    {
        $segments = explode('/', $uri);

        if ($index < 0) {
            $segments = array_reverse($segments);
            $index = abs($index) - 1;
        }

        $segment = isset($segments[$index]) ? $segments[$index] : '';

        return $segment;
    }
}
