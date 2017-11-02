# Changelog

All notable changes to `spatie/laravel-paginateroute` will be documented in this file.

## 2.6.3 - 2017-11-02
- Fixed: Wrong `Request` facade import

## 2.6.2 - 2017-11-02
- Fixed: `pageUrl` method query bug

## 2.6.0 - 2017-10-30
- Added: Preserve query string on pagination urls

## 2.5.0 - 2017-05-15
- Added: Support for Arabic

## 2.4.1 - 2017-02-01
- Laravel 5.4 fixes

## 2.4.0 - 2017-01-27
- Laravel 5.4 compatibility

## 2.3.0 - 2016-12-16
- Return route from paginate macro to allow chaining

## 2.2.2 - 2016-05-20
-  Bugfix: Fixed `it` lang file

## 2.2.1 - 2016-05-05
- Bugfix: make the package play nice in situations where a route could not be determined.

## 2.2.0
- Added: Ability to add classes to pagination list items
- Added: Ability to render next/previous links alongside pagination lists

## 2.1.0
- Feature: added `renderRelLink()`

## 2.0.2
- Bugfix: fix the Laravel 5.0 bug introduced in 2.0.1

## 2.0.1

IMPORTANT: This version contains a bug that'll break Laravel 5.0 apps

- Bugfix: routes with parameters will now render correctly

## 2.0.0

Please refer to the [upgrading section](README.md#upgrading) for more info.

- Internal refactor that changes the generated routes from the `paginate` macro
- Added: `addPageQuery` function

## 1.6.1
- Bugfix: Url's can now be correcly generated via laravel's `action()` method

## 1.6.0
- renderHtml is deprecated in favor of renderPageList
- Added: More languages
- Some big internal refactors

## 1.5.0
- Added: allUrls function
- Added: renderHtml function

## 1.4.0
- Test coverage
- Removed spatie/string dependency

## 1.3.0
- Added: Hebrew translation
- Minor refactors

## 1.2.0
- Added: Portuguese translation

## 1.1.1
- Bugfix: Parameterless route should trump the parameter route
- Bugfix: URL generation is more reliable

## 1.1.0
- PHP version requirement is now 5.4
- Added: pagination url functions

## 1.0.0
- First release!
