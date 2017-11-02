<?php

namespace Spatie\PaginateRoute;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\RouteParameterBinder;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Request;
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
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var string
     */
    protected $pageKeyword;

    /**
     * @param \Illuminate\Translation\Translator         $translator
     * @param \Illuminate\Routing\Router                 $router
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    public function __construct(Translator $translator, Router $router, UrlGenerator $urlGenerator)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->urlGenerator = $urlGenerator;

        // Unfortunately we can't do this in the service provider since routes are booted first
        $this->translator->addNamespace('paginateroute', __DIR__.'/../resources/lang');

        $this->pageKeyword = $this->translator->get('paginateroute::paginateroute.page');
    }

    /**
     * Return the current page.
     *
     * @return int
     */
    public function currentPage()
    {
        $currentRoute = $this->router->getCurrentRoute();

        if (! $currentRoute) {
            return 1;
        }

        $query = $currentRoute->parameter('pageQuery');

        return (int) str_replace($this->pageKeyword.'/', '', $query) ?: 1;
    }

    /**
     * Check if the given page is the current page.
     *
     * @param int $page
     *
     * @return bool
     */
    public function isCurrentPage($page)
    {
        return $this->currentPage() === $page;
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
        if (! $paginator->hasMorePages()) {
            return;
        }

        return $this->currentPage() + 1;
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
     * Get the next page URL.
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

        return $this->pageUrl($nextPage);
    }

    /**
     * Get the previous page number.
     *
     * @return string|null
     */
    public function previousPage()
    {
        if ($this->currentPage() <= 1) {
            return;
        }

        return $this->currentPage() - 1;
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
     * Get the previous page URL.
     *
     * @param bool $full Return the full version of the URL in for the first page
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

        return $this->pageUrl($previousPage, $full);
    }

    /**
     * Get all urls in an array.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param bool                                                  $full      Return the full version of the URL in for the first page
     *                                                                         Ex. /users/page/1 instead of /users
     *
     * @return array
     */
    public function allUrls(LengthAwarePaginator $paginator, $full = false)
    {
        if (! $paginator->hasPages()) {
            return [];
        }

        $urls = [];

        for ($page = 1; $page <= $paginator->lastPage(); ++$page) {
            $urls[] = $this->pageUrl($page, $full);
        }

        return $urls;
    }

    /**
     * Render a plain html list with previous, next and all urls. The current page gets a current class on the list item.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param bool                                                  $full              Return the full version of the URL in for the first page
     *                                                                                 Ex. /users/page/1 instead of /users
     * @param string                                                $class             Include class on pagination list
     *                                                                                 Ex. <ul class="pagination">
     * @param bool                                                  $additionalLinks   Include prev and next links on pagination list
     *
     * @return string
     */
    public function renderPageList(LengthAwarePaginator $paginator, $full = false, $class = null, $additionalLinks = false)
    {
        $urls = $this->allUrls($paginator, $full);

        if ($class) {
            $class = " class=\"$class\"";
        }

        $listItems = "<ul{$class}>";

        if ($this->hasPreviousPage() && $additionalLinks) {
            $listItems .= "<li><a href=\"{$this->previousPageUrl()}\">&laquo;</a></li>";
        }

        foreach ($urls as $i => $url) {
            $pageNum = $i + 1;
            $css = '';

            if ($pageNum == $this->currentPage()) {
                $css = ' class="active"';
            }

            $listItems .= "<li{$css}><a href=\"{$url}\">{$pageNum}</a></li>";
        }

        if ($this->hasNextPage($paginator) && $additionalLinks) {
            $listItems .= "<li><a href=\"{$this->nextPageUrl($paginator)}\">&raquo;</a></li>";
        }

        $listItems .= '</ul>';

        return $listItems;
    }

    /**
     * Render html link tags for SEO indication of previous and next page.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param bool                                                  $full       Return the full version of the URL in for the first page
     *                                                                          Ex. /users/page/1 instead of /users
     *
     * @return string
     */
    public function renderRelLinks(LengthAwarePaginator $paginator, $full = false)
    {
        $urls = $this->allUrls($paginator, $full);

        $linkItems = '';

        foreach ($urls as $i => $url) {
            $pageNum = $i + 1;

            switch ($pageNum - $this->currentPage()) {
                case -1:
                    $linkItems .= "<link rel=\"prev\" href=\"{$url}\" />";
                    break;
                case 1:
                    $linkItems .= "<link rel=\"next\" href=\"{$url}\" />";
                    break;
            }
        }

        return $linkItems;
    }

    /**
     * @deprecated in favor of renderPageList.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param bool                                                  $full      Return the full version of the URL in for the first page
     *                                                                         Ex. /users/page/1 instead of /users
     *
     * @return string
     */
    public function renderHtml(LengthAwarePaginator $paginator, $full = false)
    {
        return $this->renderPageList($paginator, $full);
    }

    /**
     * Generate a page URL, based on the request's current URL.
     *
     * @param int  $page
     * @param bool $full Return the full version of the URL in for the first page
     *                   Ex. /users/page/1 instead of /users
     *
     * @return string
     */
    public function pageUrl($page, $full = false)
    {
        $currentPageUrl = $this->router->getCurrentRoute()->uri();

        $url = $this->addPageQuery(str_replace('{pageQuery?}', '', $currentPageUrl), $page, $full);

        foreach ((new RouteParameterBinder($this->router->getCurrentRoute()))->parameters(app('request')) as $parameterName => $parameterValue) {
            $url = str_replace(['{'.$parameterName.'}', '{'.$parameterName.'?}'], $parameterValue, $url);
        }

        $query = Request::getQueryString();

        $query = $query
            ? '?'.$query
            : '';

        return $this->urlGenerator->to($url).$query;
    }

    /**
     * Append the page query to a URL.
     *
     * @param string $url
     * @param int    $page
     * @param bool   $full Return the full version of the URL in for the first page
     *                     Ex. /users/page/1 instead of /users
     *
     * @return string
     */
    public function addPageQuery($url, $page, $full = false)
    {
        // If the first page's URL is requested and $full is set to false, there's nothing to be added.
        if ($page === 1 && ! $full) {
            return $url;
        }

        return trim($url, '/')."/{$this->pageKeyword}/{$page}";
    }

    /**
     * Register the Route::paginate macro.
     */
    public function registerMacros()
    {
        $pageKeyword = $this->pageKeyword;
        $router = $this->router;

        $router->macro('paginate', function ($uri, $action) use ($pageKeyword, $router) {
            $route = null;

            $router->group(
                ['middleware' => 'Spatie\PaginateRoute\SetPageMiddleware'],
                function () use ($pageKeyword, $router, $uri, $action, &$route) {
                    $route = $router->get($uri.'/{pageQuery?}', $action)->where('pageQuery', $pageKeyword.'/[0-9]+');
                });

            return $route;
        });
    }
}
