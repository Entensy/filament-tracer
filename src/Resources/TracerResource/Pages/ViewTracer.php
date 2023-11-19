<?php

namespace Entensy\FilamentTracer\Resources\TracerResource\Pages;

use Filament\Resources\Pages\ViewRecord;

class ViewTracer extends ViewRecord
{
    public static function getResource(): string
    {
        return config('filament-tracer.filament.resource');
    }

    protected function getActions(): array
    {
        return [];
    }
}
