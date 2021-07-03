# Laravel Version

[![Build Status](https://travis-ci.org/mnabialek/laravel-version.svg?branch=master)](https://travis-ci.org/mnabialek/laravel-version)
[![Coverage Status](https://coveralls.io/repos/github/mnabialek/laravel-version/badge.svg)](https://coveralls.io/github/mnabialek/laravel-version)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mnabialek/laravel-version/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mnabialek/laravel-version/)
[![Packagist](https://img.shields.io/packagist/dt/mnabialek/laravel-version.svg)](https://packagist.org/packages/mnabialek/laravel-version)

This package let you verify whether current application is `Laravel` or `Lumen`, get application version and verify whether application is minimum at given version string.

## Installation

1. Run
   ```php   
   composer require mnabialek/laravel-version
   ```     
   in console to install this module
   
2. That's it! Installation is complete. You don't need to adjust any config or install service providers.

## Usage

Just run:

```php
$version = app()->make(\Mnabialek\LaravelVersion\Version);
```

(you can obviously use dependency injection) and then use one of available methods for example like this

```php
$result = $version->isLaravel();
```

or

```php
if ($version->isLaravel()) {
   // do something
}

```

## Available methods
 
* `isLaravel()` - verify whether application is Laravel (true for Laravel, false for Lumen) 
* `isLumen()` - verify whether application is Lumen (true for Lumen, false for Laravel)
* `full()` - get full version string (keep in mind for Lumen it can be for example: 'Lumen (5.5.2) (Laravel Components 5.5.*)') 
* `get()` - get version (for Both Laravel and Lumen it will contain only version number for example
 5.5.2)
 * `min($checkedVersion)` - verify whether application is minimum at given version. As 
 `$checkedVersion` you should pass version you want to verify for example 5.5 or 5.5.21

## Licence

This package is licenced under the [MIT license](https://github.com/mnabialek/laravel-version/blob/master/LICENSE)
