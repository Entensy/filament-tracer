<?php

namespace Entensy\FilamentTracer\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Entensy\FilamentTracer\Concerns\CanChangeState;
use Entensy\FilamentTracer\Concerns\HasSourceExpectation;

class TracesEntry extends Entry
{
    use CanChangeState;
    use HasSourceExpectation;

    protected string $view = 'filament-tracer::infolists.components.traces-entry';

    public function getDefaultChangeState(): mixed
    {
        return explode("\n", $this->getState());
    }

    public function splitTrace(string $trace): array
    {
        return explode(' ', $trace);
    }
}
