<?php

namespace Entensy\FilamentTracer\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Entensy\FilamentTracer\Concerns\CanChangeState;

class CookiesEntry extends Entry
{
    use CanChangeState;

    protected string $view = 'filament-tracer::infolists.components.cookies-entry';

    public function getDefaultChangeState(): mixed
    {
        return \json_decode($this->getState(), associative: true) ?? [];
    }

    public function hideNullValues(): bool
    {
        return config('filament-tracer.filament.cookies.hide_null_values');
    }
}
