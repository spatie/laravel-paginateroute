# Laravel Paginate Route

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-paginateroute.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-paginateroute)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpspatie/laravel-paginateroute.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpspatie/laravel-paginateroute)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-paginateroute.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-paginateroute)

This package adds the `paginate` route method to support pagination via custom routes instead of query strings. This also allows for easily translatable pagination routes ex. `/news/page/2`, `/nieuws/pagina/2`.

This is currently a relatively small package that doesn't cover all use cases, make sure you check out the limitations section of this readme.

## Install

Via Composer

``` bash
$ composer require spatie/laravel-paginateroute
```

First register the service provider in your application.

``` php
// config/app.php

'providers' => [
    ...
    'Spatie\PaginateRoute\PaginateRouteServiceProvider',
];
```

Then register the macros in `App\Providers\RouteServiceProder::boot()`.

``` php
// app/Providers/RouteServiceProvider.php

public function boot(Router $router)
{
    $this->app['paginateroute']->registerMacros();
    
    parent::boot($router);
}
```

## Usage

The `paginate` route macro will register two routes for you.

``` php
// app/Http/routes.php

// Generates /users & /users/page/{page}
Route::paginate('users', 'UsersController@index');

```

In your route's action you can just use laravel's regular pagination methods.

``` php
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

Since laravel's paginator url's will still use a query string, PaginateRoute has it's own url generator and page helper functions.

The `nextPage` functions require the paginator instance as a parameter, so they can determine whether there are any more records.

``` php
/**
 * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
 * @return int|null
 */
public function nextPage(Paginator $paginator)
```

``` php
/**
 * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
 * @return bool
 */
public function hasNextPage(Paginator $paginator)
```

``` php
/**
 * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
 * @return string|null
 */
public function nextPageUrl(Paginator $paginator)
```

``` php
/**
 * @return int|null
 */
public function previousPage()
```

``` php
/**
 * @return bool
 */
public function hasPreviousPage()
```

``` php
/**
 * @param  bool $full
 * @return string|null
 */
public function previousPageUrl($full = false)
```

If `$full` is true, the first page will be a fully qualified url. Ex. `/users/page/1` instead if just `/users` (this is the default).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
