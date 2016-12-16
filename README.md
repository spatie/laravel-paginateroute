# Laravel Paginate Route

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-paginateroute.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-paginateroute)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-paginateroute.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-paginateroute)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-paginateroute.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-paginateroute)
[![StyleCI](https://styleci.io/repos/36791555/shield?branch=master)](https://styleci.io/repos/36791555)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-paginateroute.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-paginateroute)

This package adds the `paginate` route method to support pagination via custom routes instead of query strings. This also allows for easily translatable pagination routes ex. `/news/page/2`, `/nieuws/pagina/2`.

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

**Note:** If you're upgrading to 2.0, check out the [upgrade guide](#upgrading) below.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment you are required to send us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

The best postcards will get published on the open source page on our website.

## Install

Via Composer

``` bash
$ composer require spatie/laravel-paginateroute
```

First register the service provider and facade in your application.

```php
// config/app.php

'providers' => [
    ...
    'Spatie\PaginateRoute\PaginateRouteServiceProvider',
];

'aliases' => [
    ...
    'PaginateRoute' => 'Spatie\PaginateRoute\PaginateRouteFacade',
];
```

Then register the macros in `App\Providers\RouteServiceProvider::boot()`.

```php
// app/Providers/RouteServiceProvider.php

use PaginateRoute;

// ...

public function boot(Router $router)
{
    PaginateRoute::registerMacros();
    
    parent::boot($router);
}
```

## Usage

The `paginate` route macro will register two routes for you.

```php
// app/Http/routes.php

// Generates /users & /users/page/{page}
Route::paginate('users', 'UsersController@index');

```

In your route's action you can just use Laravel's regular pagination methods.

```php
// app/Http/Controllers/UsersController.php

public function index()
{
    return view('users.index', ['users' => \App\User::simplePaginate(5)]);
}
```

If you want to customize or add translations for the "page" url segment, you can publish the language files.

``` bash
$ php artisan vendor:publish --provider="Spatie\PaginateRoute\PaginateRouteServiceProvider"
```

### Generating Url's

Since Laravel's paginator url's will still use a query string, PaginateRoute has it's own url generator and page helper functions.

```
{{-- $users is an instance of \Illuminate\Contracts\Pagination\Paginator --}}

@if(PaginateRoute::hasPreviousPage())
  <a href="{{ PaginateRoute::previousPageUrl() }}">Previous</a>
@endif

@if(PaginateRoute::hasNextPage($users))
  <a href="{{ PaginateRoute::nextPageUrl($users) }}">Next</a>
@endif
```

The `nextPage` functions require the paginator instance as a parameter, so they can determine whether there are any more records.

```php
/**
 * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
 * @return int|null
 */
public function nextPage(Paginator $paginator)
```

```php
/**
 * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
 * @return bool
 */
public function hasNextPage(Paginator $paginator)
```

```php
/**
 * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
 * @return string|null
 */
public function nextPageUrl(Paginator $paginator)
```

```php
/**
 * @return int|null
 */
public function previousPage()
```

```php
/**
 * @return bool
 */
public function hasPreviousPage()
```

```php
/**
 * @param  bool $full
 * @return string|null
 */
public function previousPageUrl($full = false)
```

```php
/**
 * @param int  $page
 * @param bool $full
 * @return string
 */
public function pageUrl($page, $full = false)
```

If `$full` is true, the first page will be a fully qualified url. Ex. `/users/page/1` instead if just `/users` (this is the default).

To retrieve the url of a specific page of a paginated route, that isn't the current route, there's the `addPageQuery` function.

```php
/**
 * @param string $url
 * @param int $page
 * @param bool $full
 * @return string
 */
public function addPageQuery($url, $page, $full = false)
```

You can also retrieve an array with all available urls. These can be rendered as a plain html list with page numbers. Note that these functions require a `LengthAwarePaginator`.

```php
/**
 * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
 * @param  bool $full
 * @return array
 */
public function allUrls(LengthAwarePaginator $paginator, $full = false)
```

```php
/**
 * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
 * @param  bool $full
 * @param  string $class
 * @param  bool $additionalLinks
 * @return string
 */
public function renderPageList(LengthAwarePaginator $paginator, $full = false, $class = null, $additionalLinks = false)
```

```html
<!-- Example output: -->
<ul class="pagination">
    <li><a href="http://example.com/news">1</a></li>
    <li><a href="http://example.com/news/page/2">2</a></li>
    <li class="active"><a href="http://example.com/news/page/3">3</a></li>
    <li><a href="http://example.com/news/page/4">4</a></li>
    <li><a href="http://example.com/news/page/4">&raquo;</a></li>
</ul>
```

You can render link tags to mark previous and next page for SEO. Note that these functions require a `LengthAwarePaginator`.

```php
/**
 * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
 * @param  bool $full
 * @return string
 */
public function renderRelLinks(LengthAwarePaginator $paginator, $full = false)
```

```html
<!-- Example output: -->
<link rel="prev" href="http://example.com/news/page/2" />
<link rel="next" href="http://example.com/news/page/4" />
```

## Tests

The package contains some integration/smoke tests, set up with Orchestra. The tests can be run via phpunit.

```
$ phpunit
```

## Upgrading

### 1.x => 2.0

The 2.0 release changes the route macro to only register one route with the entire query in it, so providing a page parameter to the action link is no longer possible.

For example, `action('FooController@bar', ['page' => 3])` is no longer possible, and should be replaced by `PaginateRoute::addPageQuery(action('FooController@bar'), 3)`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
