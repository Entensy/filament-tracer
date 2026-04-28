<?php

namespace Entensy\FilamentTracer\Resources\TracerResource\Pages;

use Entensy\FilamentTracer\FilamentTracerPlugin;
use Entensy\FilamentTracer\Widgets\TracerStatsOverviewWidget;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListTracers extends ListRecords
{
    use ExposesTableToWidgets;

    public static function getResource(): string
    {
        return config('filament-tracer.filament.resource');
    }

    /**
     * @return array<\Filament\Actions\Action | \Filament\Actions\ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | \Filament\Widgets\WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        $widgets = [];

        if (FilamentTracerPlugin::filament()->hasStatsWidget()) {
            $widgets[] = TracerStatsOverviewWidget::class;
        }

        return $widgets;
    }
}
