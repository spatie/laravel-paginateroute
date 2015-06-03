# Laravel Paginate Route

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-paginateroute.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-paginateroute)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/thephpspatie/laravel-paginateroute/master.svg?style=flat-square)](https://travis-ci.org/thephpspatie/laravel-paginateroute)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpspatie/laravel-paginateroute.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpspatie/laravel-paginateroute/code-structure)
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
$ art vendor:publish --provider="Spatie\PaginateRoute\PaginateRouteServiceProvider"
```


## Limitations

Laravel's paginator url functions still returns the default query-string version of the url, so you'll need to add some sort of logic to your controller action or views to generate next page, previous page and other urls.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Sebastian De Deybe](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
