<?php

namespace Entensy\FilamentTracer\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Entensy\FilamentTracer\Concerns\CanChangeState;

class HeadersEntry extends Entry
{
    use CanChangeState;

    protected string $view = 'filament-tracer::infolists.components.headers-entry';

    public function getDefaultChangeState(): mixed
    {
        return \json_decode($this->getState(), associative: true) ?? [];
    }
}
