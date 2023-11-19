<?php

namespace Entensy\FilamentTracer\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Entensy\FilamentTracer\FilamentTracerPlugin;
use Entensy\FilamentTracer\Infolists\Components\BodyEntry;
use Entensy\FilamentTracer\Infolists\Components\CookiesEntry;
use Entensy\FilamentTracer\Infolists\Components\HeadersEntry;
use Entensy\FilamentTracer\Infolists\Components\QueriesEntry;
use Entensy\FilamentTracer\Infolists\Components\TracesEntry;

class TracerResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModel(): string
    {
        return config('filament-tracer.model');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('tracer_details')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('details')
                            ->label(__('filament-tracer::labels.tabs.details'))
                            ->schema([
                                Infolists\Components\Split::make([
                                    Infolists\Components\Grid::make(1)
                                        ->grow()
                                        ->schema([
                                            Infolists\Components\Section::make('')
                                                ->columns()
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('source')
                                                        ->label(__('filament-tracer::labels.source'))
                                                        ->badge()
                                                        ->color('success'),
                                                    Infolists\Components\TextEntry::make('error_type')
                                                        ->label(__('filament-tracer::labels.error_type'))
                                                        ->badge()
                                                        ->color('danger'),
                                                    Infolists\Components\TextEntry::make('path')
                                                        ->label(__('filament-tracer::labels.path')),
                                                    Infolists\Components\TextEntry::make('ip')
                                                        ->label(__('filament-tracer::labels.ip'))
                                                        ->badge()
                                                        ->color('gray'),
                                                ]),
                                            Infolists\Components\Fieldset::make('')
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('code')
                                                        ->label(__('filament-tracer::labels.code'))
                                                        ->badge()
                                                        ->color('warning')
                                                        ->inlineLabel(),
                                                    Infolists\Components\TextEntry::make('message')
                                                        ->label(__('filament-tracer::labels.message'))
                                                        ->columnSpanFull(),
                                                ]),
                                            Infolists\Components\Section::make('')
                                                ->schema([
                                                    Infolists\Components\Split::make([
                                                        Infolists\Components\TextEntry::make('line')
                                                            ->label(__('filament-tracer::labels.line'))
                                                            ->badge()
                                                            ->color('warning')
                                                            ->grow(false),
                                                        Infolists\Components\TextEntry::make('method')
                                                            ->label(__('filament-tracer::labels.method'))
                                                            ->weight(FontWeight::SemiBold),
                                                    ])
                                                        ->columns(),
                                                    Infolists\Components\TextEntry::make('file')
                                                        ->label(__('filament-tracer::labels.file')),
                                                ]),
                                        ]),
                                ]),
                            ]),
                        Infolists\Components\Tabs\Tab::make('exceptions')
                            ->label(__('filament-tracer::labels.tabs.exceptions'))
                            ->badge(
                                fn ($record) => FilamentTracerPlugin::filament()->getTracesCounter($record)
                            )
                            ->schema([
                                TracesEntry::make('traces'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('queries')
                            ->label(__('filament-tracer::labels.tabs.queries'))
                            ->badge(
                                fn ($record) => FilamentTracerPlugin::filament()->getQueriesCounter($record)
                            )
                            ->schema([
                                QueriesEntry::make('queries'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('body')
                            ->label(__('filament-tracer::labels.tabs.body'))
                            ->badge(
                                fn ($record) => FilamentTracerPlugin::filament()->getBodyCounter($record)
                            )
                            ->schema([
                                BodyEntry::make('body'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('headers')
                            ->label(__('filament-tracer::labels.tabs.headers'))
                            ->badge(
                                fn ($record) => FilamentTracerPlugin::filament()->getHeadersCounter($record)
                            )
                            ->schema([
                                HeadersEntry::make('headers'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('cookies')
                            ->label(__('filament-tracer::labels.tabs.cookies'))
                            ->badge(
                                fn ($record) => FilamentTracerPlugin::filament()->getCookiesCounter($record)
                            )
                            ->schema([
                                CookiesEntry::make('cookies'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(config('filament-tracer.database.primary_key'))
                    ->label(__('filament-tracer::labels.id'))
                    ->toggleable(config('filament-tracer.filament.table.id_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.id_sortable')),
                Tables\Columns\TextColumn::make('source')
                    ->label(__('filament-tracer::labels.source'))
                    ->badge()
                    ->color('success')
                    ->toggleable(config('filament-tracer.filament.table.source_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.source_sortable'))
                    ->searchable(config('filament-tracer.filament.table.source_searchable')),
                Tables\Columns\TextColumn::make('error_type')
                    ->label(__('filament-tracer::labels.error_type'))
                    ->badge()
                    ->color('danger')
                    ->toggleable(config('filament-tracer.filament.table.error_type_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.error_type_sortable'))
                    ->searchable(config('filament-tracer.filament.table.error_type_searchable')),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('filament-tracer::labels.code'))
                    ->badge()
                    ->color('warning')
                    ->toggleable(config('filament-tracer.filament.table.code_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.code_sortable'))
                    ->searchable(config('filament-tracer.filament.table.code_searchable')),
                Tables\Columns\TextColumn::make('file')
                    ->label(__('filament-tracer::labels.file'))
                    ->toggleable(config('filament-tracer.filament.table.file_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.file_sortable'))
                    ->searchable(config('filament-tracer.filament.table.file_searchable')),
                Tables\Columns\TextColumn::make('line')
                    ->label(__('filament-tracer::labels.line'))
                    ->badge()
                    ->color('warning')
                    ->toggleable(config('filament-tracer.filament.table.line_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.line_sortable')),
                Tables\Columns\TextColumn::make('method')
                    ->label(__('filament-tracer::labels.method'))
                    ->toggleable(config('filament-tracer.filament.table.method_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.method_sortable'))
                    ->searchable(config('filament-tracer.filament.table.method_searchable')),
                Tables\Columns\TextColumn::make('ip')
                    ->label(__('filament-tracer::labels.ip'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(config('filament-tracer.filament.table.ip_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.ip_sortable'))
                    ->searchable(config('filament-tracer.filament.table.ip_searchable')),
                Tables\Columns\TextColumn::make('path')
                    ->label(__('filament-tracer::labels.path'))
                    ->limit(config('filament-tracer.filament.table.path_text_limit', 64))
                    ->toggleable(config('filament-tracer.filament.table.path_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.path_sortable'))
                    ->searchable(config('filament-tracer.filament.table.path_searchable')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-tracer::labels.created_at'))
                    ->toggleable(config('filament-tracer.filament.table.created_at_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.created_at_sortable'))
                    ->dateTime(config('filament-tracer.filament.table.created_at_format', 'd/m/Y H:i:s')),
            ])
            ->defaultSort(config('filament-tracer.filament.table.default_sort'), config('filament-tracer.filament.table.sort_direction', 'desc'))
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament-tracer::labels.created_from', ['date' => '']))
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament-tracer::labels.created_until', ['date' => '']))
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = __('filament-tracer::labels.created_from', ['date' => Carbon::parse($data['created_from'])->toFormattedDateString()]);
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = __('filament-tracer::labels.created_until', ['date' => Carbon::parse($data['created_until'])->toFormattedDateString()]);
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(config('filament-tracer.filament.table.enable_bulk_delete', true)),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        $list = config('filament-tracer.filament.list');
        $view = config('filament-tracer.filament.view');

        return [
            'index' => $list::route('/'),
            'view' => $view::route('/{record}'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament-tracer::labels.navigation.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-tracer::labels.navigation.label.plural');
    }

    public static function getSlug(): string
    {
        return config('filament-tracer.filament.slug');
    }

    public static function getNavigationBadge(): ?string
    {
        if ((bool) config('filament-tracer.filament.navigation.enable_badge')) {
            return static::getModel()::count();
        }

        return null;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-tracer::labels.navigation.group');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) config('filament-tracer.filament.navigation.enabled');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-tracer::labels.navigation.label.plural');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-tracer.filament.navigation.icon');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-tracer.filament.navigation.sort');
    }

    public static function canGloballySearch(): bool
    {
        return false;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [];
    }
}
