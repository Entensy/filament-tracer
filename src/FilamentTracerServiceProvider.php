<?php

namespace Entensy\FilamentTracer;

use Spatie\LaravelPackageTools\Package;
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
            ->hasTranslations();
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
        //
    }
}
