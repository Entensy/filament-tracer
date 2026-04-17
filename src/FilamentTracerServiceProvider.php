<?php

namespace Entensy\FilamentTracer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTracerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-tracer')
            ->hasConfigFile()
            ->hasMigration('create_tracers_table')
            ->hasTranslations();
    }
}
