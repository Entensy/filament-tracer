<?php

namespace Entensy\FilamentTracer;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Spatie\LaravelPackageTools\Package;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTracerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-tracer')
            ->hasConfigFile()
            ->hasMigration('create_tracers_table')
            ->hasTranslations()
            ->hasViews();

        // ->hasRoutes([
        //     __DIR__ . '../routes/web.php'
        // ])
        // ->hasAssets()
        // ->hasViews()
        // ->hasMigrations([
        //     'create_example_table',
        // ])
        // ->hasCommands([
        //     FilamentTracerCommand::class
        // ])
    }

    public function registeringPackage(): void
    {
        //
    }

    public function packageRegistered(): void
    {
        //
    }

    public function bootingPackage(): void
    {
        //
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('filament-tracer', __DIR__ . '/../resources/dist/filament-tracer.js'),
            Css::make('filament-tracer', __DIR__ . '/../resources/dist/filament-tracer.css'),
        ], package: 'entensy/filament-tracer');
    }
}
