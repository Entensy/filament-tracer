<?php

namespace Entensy\FilamentTracer\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Entensy\FilamentTracer\Concerns\CanChangeState;
use Entensy\FilamentTracer\Concerns\HasSourceExpectation;

class QueriesEntry extends Entry
{
    use CanChangeState;
    use HasSourceExpectation;

    protected string $view = 'filament-tracer::infolists.components.queries-entry';

    public function getDefaultChangeState(): mixed
    {
        return \json_decode($this->getState(), associative: true);
    }

    public function joinQueryWithBindings(string $query, array $bindings): string
    {
        return str($query)->replace(['?'], $bindings)->toString();
    }
}
