<?php

namespace Entensy\FilamentTracer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Entensy\FilamentTracer\FilamentTracer
 */
class FilamentTracer extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Entensy\FilamentTracer\FilamentTracer::class;
    }
}
