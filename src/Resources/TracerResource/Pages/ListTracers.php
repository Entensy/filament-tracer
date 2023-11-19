<?php

namespace Entensy\FilamentTracer\Resources\TracerResource\Pages;

use Filament\Resources\Pages\ListRecords;

class ListTracers extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-tracer.filament.resource');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
