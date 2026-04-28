<?php

namespace Entensy\FilamentTracer\Resources\TracerResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Js;

class ViewTracer extends ViewRecord
{
    public static function getResource(): string
    {
        return config('filament-tracer.filament.resource');
    }

    /**
     * @return array<\Filament\Actions\Action | \Filament\Actions\ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('copy_report')
                ->label(__('filament-tracer::labels.actions.copy_report'))
                ->icon('heroicon-o-clipboard-document')
                ->color('gray')
                ->action(function (): void {
                    $markdown = method_exists($this->record, 'toMarkdownReport')
                        ? (string) $this->record->toMarkdownReport()
                        : '';

                    $payload = Js::from($markdown);

                    $this->js(<<<JS
                        window.navigator.clipboard.writeText({$payload})
                    JS);

                    Notification::make()
                        ->title(__('filament-tracer::labels.actions.copied_title'))
                        ->body(__('filament-tracer::labels.actions.copied_body'))
                        ->success()
                        ->send();
                }),

            DeleteAction::make()
                ->visible((bool) config('filament-tracer.filament.view.enable_delete', true)),
        ];
    }
}
