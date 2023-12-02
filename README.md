# filament-tracer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/entensy/filament-tracer.svg?style=flat-square)](https://packagist.org/packages/entensy/filament-tracer)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/entensy/filament-tracer/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/entensy/filament-tracer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/entensy/filament-tracer/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/entensy/filament-tracer/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/entensy/filament-tracer.svg?style=flat-square)](https://packagist.org/packages/entensy/filament-tracer)

Filament tracer is a flexible filament plugin to report and view exceptions and
errors with a generic table schema to be able to insert traces and errors into
the table in any programming languages.

The purpose of this package is to have a common table to report/log any type of
errors throughout your applications, independent of what programming language
you use in your application so long as your application has a connection to the
database, you should be able to insert errors and logs and view them in one
place.

## Installation

This plugin requires Filament v3.0+, it does not work in older version!

Install the package via composer:

```bash
composer require entensy/filament-tracer
```

You could also use direct repository url in your `composer.json`:

```json
"require": {
  "entensy/filament-tracer": "dev-main"
}
"repositories": [
  {
    "type": "git",
    "url": "https://github.com/entensy/filament-tracer.git"
  }
]
```

## Usage

Register the plugin in your desired filament panel:

```php
public function panel(Panel $panel): Panel
{
    return $panel
            ...
            ->plugins([
                FilamentTracerPlugin::make()
                    // You may define how you would like to get tab badge numbers, these must return int type
                    ->tracesCounterUsing(fn($record) => count( explode(PHP_EOL, $record->traces) ) ?? 0)
                    ->queriesCounterUsing(fn($record) => /** return int value */)
                    ->bodyCounterUsing(fn($record) => /** return int value */)
                    ->headersCounterUsing(fn($record) => /** return int value */)
                    ->cookiesCounterUsing(fn($record) => /** return int value */)
            ]);
}
```

To register capturing exceptions and errors, go to your
`app\Exceptions\Handler.php` file and put the following snippet into `register`
method:

```php
$this->reportable(function (Throwable $e) {
    if ($this->shouldReport($e)) {
        \Entensy\FilamentTracer\FilamentTracer::capture($e, request());
    }
});
```

### Configuration

You may publish configuration using Laravel's publish command:

```bash
# Publish config file
php artisan vendor:publish --tag=filament-tracer-config

# Publish views
php artisan vendor:publish --tag=filament-tracer-views

# Publish translations
php artisan vendor:publish --tag=filament-tracer-translations

# Publish migrations
php artisan vendor:publish --tag=filament-tracer-migrations
```

### Custom Tracer Class

You may write your own tracer class by changing the default class in the plugin
config file. If you don't have this file, you may publish it with
`php artisan vendor:publish --tag=filament-tracer-config` file:

```php
[
...
    // You may implement your own tracer by implementing Tracerable interface
    'tracer' => \Entensy\FilamentTracer\DefaultTracer::class,
...
]
```

Defining a custom Tracer class has to implement `Tracerable` interface.

```php
use Entensy\FilamentTracer\Contracts\Tracerable;

class MyCustomTracer implements Tracerable
{
    //
}
```

If you would like to change how an error being stored, you may overwrite this
implementation by implementing `HasStore` interface in your custom tracer class
then add your implementation in `store` method

```php
use Entensy\FilamentTracer\Contracts\HasStore;
use Entensy\FilamentTracer\Contracts\Tracerable;

class MyCustomTracer implements Tracerable, HasStore
{
    public function store(): mixed
    {
        $err = $this->getThrowable():

        // just log the trace and don't store it in database
        logger()->error($err);

        return true;
    }
}
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed
recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

Please email `security@entensy.com` for any security issues.

## Credits

-   [AlanD20](https://github.com/AlanD20)
-   [Entensy](https://github.com/entensy)

## License

This repository is under [MIT License (MIT)](LICENSE).
