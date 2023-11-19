<?php

namespace Entensy\FilamentTracer\Resources\TracerResource\Pages;

use Filament\Actions;
use App\Filament\Resources\TracerResource;
use Filament\Resources\Pages\EditRecord;

class EditTracer extends EditRecord
{
    protected static string $resource = TracerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
