<?php

namespace Entensy\FilamentTracer;

use Filament\Panel;
use Filament\Contracts\Plugin;

class FilamentTracerPlugin implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel->pages([
            //
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function getId(): string
    {
        return 'filament-tracer';
    }

    public static function make(): static
    {
        return new static;
    }
}
