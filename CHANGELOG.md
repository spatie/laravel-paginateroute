# Changelog

All Notable changes to `spatie/laravel-paginateroute` will be documented in this file.

## 2.0.0

Please defer to the [upgrading section](README.md#upgrading) for more info.

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
