<?php

namespace Entensy\FilamentTracer\Resources;

use BackedEnum;
use Carbon\Carbon;
use Entensy\FilamentTracer\Enums\Severity;
use Entensy\FilamentTracer\FilamentTracerPlugin;
use Entensy\FilamentTracer\Models\Tracer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Phiki\Grammar\Grammar;
use UnitEnum;

class TracerResource extends Resource
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bug-ant';

    public static function getModel(): string
    {
        return config('filament-tracer.model');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            static::heroSection(),

            Tabs::make('tracer_details')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make('details')
                        ->label(__('filament-tracer::labels.tabs.details'))
                        ->icon('heroicon-o-information-circle')
                        ->schema(static::detailsSchema()),

                    Tab::make('exceptions')
                        ->label(__('filament-tracer::labels.tabs.exceptions'))
                        ->icon('heroicon-o-code-bracket-square')
                        ->badge(fn ($record) => FilamentTracerPlugin::filament()->getTracesCounter($record))
                        ->badgeColor('danger')
                        ->schema([
                            CodeEntry::make('traces')
                                ->hiddenLabel()
                                ->copyable()
                                ->grammar(Grammar::Log)
                                ->placeholder(__('filament-tracer::labels.placeholders.no_traces')),
                        ]),

                    Tab::make('queries')
                        ->label(__('filament-tracer::labels.tabs.queries'))
                        ->icon('heroicon-o-circle-stack')
                        ->badge(fn ($record) => FilamentTracerPlugin::filament()->getQueriesCounter($record))
                        ->badgeColor('warning')
                        ->schema([
                            RepeatableEntry::make('queries')
                                ->hiddenLabel()
                                ->contained()
                                ->placeholder(__('filament-tracer::labels.placeholders.no_queries'))
                                ->state(fn ($record) => static::prepareQueriesState($record))
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            TextEntry::make('connection_name')
                                                ->label(__('filament-tracer::labels.queries.connection'))
                                                ->icon('heroicon-o-server-stack')
                                                ->badge()
                                                ->color('gray')
                                                ->placeholder('—'),
                                            TextEntry::make('time')
                                                ->label(__('filament-tracer::labels.queries.time'))
                                                ->icon('heroicon-o-clock')
                                                ->badge()
                                                ->color(fn ($state) => static::queryTimeColor($state))
                                                ->suffix(' ms')
                                                ->placeholder('—'),
                                            TextEntry::make('bindings_count')
                                                ->label(__('filament-tracer::labels.queries.bindings'))
                                                ->icon('heroicon-o-variable')
                                                ->badge()
                                                ->color('warning')
                                                ->visible(fn ($state) => is_numeric($state) && (int) $state > 0),
                                        ]),
                                    CodeEntry::make('sql')
                                        ->hiddenLabel()
                                        ->grammar(Grammar::Sql)
                                        ->copyable(),
                                ]),
                        ]),

                    Tab::make('body')
                        ->label(__('filament-tracer::labels.tabs.body'))
                        ->icon('heroicon-o-document-text')
                        ->badge(fn ($record) => FilamentTracerPlugin::filament()->getBodyCounter($record))
                        ->badgeColor('info')
                        ->schema([
                            KeyValueEntry::make('body')
                                ->hiddenLabel()
                                ->keyLabel(__('filament-tracer::labels.key'))
                                ->valueLabel(__('filament-tracer::labels.value'))
                                ->placeholder(__('filament-tracer::labels.placeholders.no_body')),
                        ]),

                    Tab::make('headers')
                        ->label(__('filament-tracer::labels.tabs.headers'))
                        ->icon('heroicon-o-bars-3-bottom-left')
                        ->badge(fn ($record) => FilamentTracerPlugin::filament()->getHeadersCounter($record))
                        ->badgeColor('success')
                        ->schema([
                            KeyValueEntry::make('headers')
                                ->hiddenLabel()
                                ->keyLabel(__('filament-tracer::labels.key'))
                                ->valueLabel(__('filament-tracer::labels.value'))
                                ->state(fn ($record) => static::flattenArrayValues((array) $record->headers))
                                ->placeholder(__('filament-tracer::labels.placeholders.no_headers')),
                        ]),

                    Tab::make('cookies')
                        ->label(__('filament-tracer::labels.tabs.cookies'))
                        ->icon('heroicon-o-identification')
                        ->badge(fn ($record) => FilamentTracerPlugin::filament()->getCookiesCounter($record))
                        ->badgeColor('gray')
                        ->schema([
                            KeyValueEntry::make('cookies')
                                ->hiddenLabel()
                                ->keyLabel(__('filament-tracer::labels.key'))
                                ->valueLabel(__('filament-tracer::labels.value'))
                                ->state(fn ($record) => $record->visible_cookies)
                                ->placeholder(__('filament-tracer::labels.placeholders.no_cookies')),
                        ]),
                ]),
        ]);
    }

    /**
     * The "hero" card shown above the tabs — gives you the gist of the error
     * before you start clicking into anything.
     */
    protected static function heroSection(): Section
    {
        return Section::make()
            ->columnSpanFull()
            ->icon(fn ($record) => optional($record?->severity)->icon() ?? 'heroicon-o-exclamation-triangle')
            ->iconColor(fn ($record) => optional($record?->severity)->color() ?? 'danger')
            ->heading(fn ($record) => $record?->error_type ?? __('filament-tracer::labels.navigation.label.singular'))
            ->description(fn ($record) => $record?->short_location ?: null)
            ->schema([
                TextEntry::make('message')
                    ->hiddenLabel()
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Medium)
                    ->copyable()
                    ->placeholder('—')
                    ->columnSpanFull(),

                Grid::make(5)
                    ->schema([
                        TextEntry::make('severity')
                            ->label(__('filament-tracer::labels.severity_label'))
                            ->state(fn ($record) => $record?->severity?->label())
                            ->badge()
                            ->icon(fn ($record) => $record?->severity?->icon())
                            ->color(fn ($record) => $record?->severity?->color() ?? 'danger'),
                        TextEntry::make('code')
                            ->label(__('filament-tracer::labels.code'))
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('warning')
                            ->placeholder('—'),
                        TextEntry::make('method')
                            ->label(__('filament-tracer::labels.method'))
                            ->icon('heroicon-o-arrow-right-circle')
                            ->badge()
                            ->color('gray')
                            ->placeholder('—'),
                        TextEntry::make('source')
                            ->label(__('filament-tracer::labels.source'))
                            ->icon('heroicon-o-cpu-chip')
                            ->badge()
                            ->color('success')
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->label(__('filament-tracer::labels.when'))
                            ->icon('heroicon-o-clock')
                            ->since()
                            ->tooltip(fn ($record) => optional($record?->created_at)->toDayDateTimeString())
                            ->badge()
                            ->color('info')
                            ->placeholder('—'),
                    ]),
            ]);
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    protected static function detailsSchema(): array
    {
        return [
            Section::make(__('filament-tracer::labels.sections.error'))
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->collapsible()
                ->columns(2)
                ->schema([
                    TextEntry::make('error_type')
                        ->label(__('filament-tracer::labels.error_type'))
                        ->badge()
                        ->color(fn ($record) => optional($record?->severity)->color() ?? 'danger')
                        ->icon(fn ($record) => optional($record?->severity)->icon()),
                    TextEntry::make('code')
                        ->label(__('filament-tracer::labels.code'))
                        ->badge()
                        ->color('warning')
                        ->placeholder('—'),
                    TextEntry::make('message')
                        ->label(__('filament-tracer::labels.message'))
                        ->copyable()
                        ->weight(FontWeight::Medium)
                        ->columnSpanFull(),
                ]),

            Section::make(__('filament-tracer::labels.sections.location'))
                ->icon('heroicon-o-map-pin')
                ->iconColor('warning')
                ->collapsible()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('line')
                                ->label(__('filament-tracer::labels.line'))
                                ->icon('heroicon-o-hashtag')
                                ->badge()
                                ->color('warning'),
                            TextEntry::make('method')
                                ->label(__('filament-tracer::labels.method'))
                                ->icon('heroicon-o-arrow-right-circle')
                                ->badge()
                                ->color('warning'),
                        ]),
                    TextEntry::make('file')
                        ->label(__('filament-tracer::labels.file'))
                        ->icon('heroicon-o-document')
                        ->badge()
                        ->color('gray')
                        ->copyable(),
                ]),

            Section::make(__('filament-tracer::labels.sections.request'))
                ->icon('heroicon-o-globe-alt')
                ->iconColor('info')
                ->collapsible()
                ->columns(2)
                ->schema([
                    TextEntry::make('source')
                        ->label(__('filament-tracer::labels.source'))
                        ->icon('heroicon-o-cpu-chip')
                        ->badge()
                        ->color('success'),
                    TextEntry::make('ip')
                        ->label(__('filament-tracer::labels.ip'))
                        ->icon('heroicon-o-signal')
                        ->badge()
                        ->color('gray')
                        ->copyable(),
                    TextEntry::make('path')
                        ->label(__('filament-tracer::labels.path'))
                        ->icon('heroicon-o-link')
                        ->badge()
                        ->color('info')
                        ->copyable()
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * Transform raw stored queries into a shape the RepeatableEntry can
     * render directly: interpolated SQL + a bindings count.
     *
     * @return array<int, array<string, mixed>>
     */
    protected static function prepareQueriesState(?Model $record): array
    {
        if ($record === null) {
            return [];
        }

        $queries = (array) ($record->queries ?? []);

        return array_values(array_map(function ($query) {
            $query = (array) $query;
            $bindings = (array) ($query['bindings'] ?? []);

            return [
                'connection_name' => $query['connection_name'] ?? null,
                'time' => $query['time'] ?? null,
                'bindings_count' => count($bindings),
                'sql' => Tracer::interpolateSql((string) ($query['sql'] ?? ''), $bindings),
            ];
        }, $queries));
    }

    protected static function queryTimeColor(mixed $state): string
    {
        $ms = is_numeric($state) ? (float) $state : 0;

        return match (true) {
            $ms >= 500 => 'danger',
            $ms >= 100 => 'warning',
            default => 'success',
        };
    }

    /**
     * @param  array<string, mixed>  $headers
     * @return array<string, string>
     */
    protected static function flattenArrayValues(array $headers): array
    {
        $flat = [];

        foreach ($headers as $key => $value) {
            if (is_array($value)) {
                $flat[$key] = implode(', ', array_map(fn ($v) => (string) $v, $value));
            } else {
                $flat[$key] = (string) $value;
            }
        }

        return $flat;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(config('filament-tracer.database.primary_key'))
                    ->label(__('filament-tracer::labels.id'))
                    ->toggleable(
                        isToggledHiddenByDefault: true,
                        condition: config('filament-tracer.filament.table.id_toggleable'),
                    )
                    ->sortable(config('filament-tracer.filament.table.id_sortable')),

                TextColumn::make('error_type')
                    ->label(__('filament-tracer::labels.error_type'))
                    ->badge()
                    ->icon(fn (Model $record) => $record->severity->icon())
                    ->color(fn (Model $record) => $record->severity->color())
                    ->formatStateUsing(fn ($state) => class_basename((string) $state))
                    ->tooltip(fn (Model $record) => $record->error_type)
                    ->toggleable(config('filament-tracer.filament.table.error_type_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.error_type_sortable'))
                    ->searchable(
                        isIndividual: false,
                        condition: config('filament-tracer.filament.table.error_type_searchable'),
                    ),

                TextColumn::make('message')
                    ->label(__('filament-tracer::labels.message'))
                    ->limit(80)
                    ->tooltip(fn (Model $record) => $record->message)
                    ->searchable(),

                TextColumn::make('source')
                    ->label(__('filament-tracer::labels.source'))
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-cpu-chip')
                    ->toggleable(config('filament-tracer.filament.table.source_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.source_sortable'))
                    ->searchable(condition: config('filament-tracer.filament.table.source_searchable')),

                TextColumn::make('code')
                    ->label(__('filament-tracer::labels.code'))
                    ->badge()
                    ->color('warning')
                    ->toggleable(
                        isToggledHiddenByDefault: true,
                        condition: config('filament-tracer.filament.table.code_toggleable'),
                    )
                    ->sortable(config('filament-tracer.filament.table.code_sortable'))
                    ->searchable(condition: config('filament-tracer.filament.table.code_searchable')),

                TextColumn::make('file')
                    ->label(__('filament-tracer::labels.file'))
                    ->formatStateUsing(fn ($state) => $state ? basename((string) $state) : '—')
                    ->tooltip(fn (Model $record) => $record->file)
                    ->toggleable(config('filament-tracer.filament.table.file_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.file_sortable'))
                    ->searchable(condition: config('filament-tracer.filament.table.file_searchable')),

                TextColumn::make('line')
                    ->label(__('filament-tracer::labels.line'))
                    ->badge()
                    ->color('warning')
                    ->toggleable(
                        isToggledHiddenByDefault: true,
                        condition: config('filament-tracer.filament.table.line_toggleable'),
                    )
                    ->sortable(config('filament-tracer.filament.table.line_sortable')),

                TextColumn::make('method')
                    ->label(__('filament-tracer::labels.method'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(
                        isToggledHiddenByDefault: true,
                        condition: config('filament-tracer.filament.table.method_toggleable'),
                    )
                    ->sortable(config('filament-tracer.filament.table.method_sortable'))
                    ->searchable(condition: config('filament-tracer.filament.table.method_searchable')),

                TextColumn::make('ip')
                    ->label(__('filament-tracer::labels.ip'))
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-signal')
                    ->toggleable(
                        isToggledHiddenByDefault: true,
                        condition: config('filament-tracer.filament.table.ip_toggleable'),
                    )
                    ->sortable(config('filament-tracer.filament.table.ip_sortable'))
                    ->searchable(condition: config('filament-tracer.filament.table.ip_searchable')),

                TextColumn::make('path')
                    ->label(__('filament-tracer::labels.path'))
                    ->limit(config('filament-tracer.filament.table.path_text_limit', 64))
                    ->tooltip(fn (Model $record) => $record->path)
                    ->toggleable(
                        isToggledHiddenByDefault: true,
                        condition: config('filament-tracer.filament.table.path_toggleable'),
                    )
                    ->sortable(config('filament-tracer.filament.table.path_sortable'))
                    ->searchable(condition: config('filament-tracer.filament.table.path_searchable')),

                TextColumn::make('created_at')
                    ->label(__('filament-tracer::labels.when'))
                    ->since()
                    ->tooltip(fn (Model $record) => optional($record->created_at)?->toDayDateTimeString())
                    ->toggleable(config('filament-tracer.filament.table.created_at_toggleable'))
                    ->sortable(config('filament-tracer.filament.table.created_at_sortable')),
            ])
            ->defaultSort(
                config('filament-tracer.filament.table.default_sort'),
                config('filament-tracer.filament.table.sort_direction', 'desc'),
            )
            ->groups([
                Group::make('created_at')
                    ->label(__('filament-tracer::labels.when'))
                    ->date()
                    ->collapsible(),
                Group::make('error_type')
                    ->label(__('filament-tracer::labels.error_type'))
                    ->collapsible(),
                Group::make('source')
                    ->label(__('filament-tracer::labels.source'))
                    ->collapsible(),
            ])
            ->striped()
            ->filters([
                SelectFilter::make('error_type')
                    ->label(__('filament-tracer::labels.error_type'))
                    ->multiple()
                    ->options(fn () => static::getModel()::query()
                        ->select('error_type')
                        ->distinct()
                        ->orderBy('error_type')
                        ->pluck('error_type', 'error_type')
                        ->toArray()),

                SelectFilter::make('source')
                    ->label(__('filament-tracer::labels.source'))
                    ->multiple()
                    ->options(fn () => static::getModel()::query()
                        ->select('source')
                        ->distinct()
                        ->orderBy('source')
                        ->pluck('source', 'source')
                        ->toArray()),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('filament-tracer::labels.created_from', ['date' => '']))
                            ->native(false)
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        DatePicker::make('created_until')
                            ->label(__('filament-tracer::labels.created_until', ['date' => '']))
                            ->native(false)
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
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->tooltip(__('filament-tracer::labels.actions.view_tooltip')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(config('filament-tracer.filament.table.enable_bulk_delete', true)),
                ]),
            ])
            ->emptyStateHeading(__('filament-tracer::labels.empty_state.heading'))
            ->emptyStateDescription(__('filament-tracer::labels.empty_state.description'))
            ->emptyStateIcon('heroicon-o-check-badge');
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

    public static function getWidgets(): array
    {
        return [
            \Entensy\FilamentTracer\Widgets\TracerStatsOverviewWidget::class,
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

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return config('filament-tracer.filament.slug');
    }

    public static function getNavigationBadge(): ?string
    {
        if ((bool) config('filament-tracer.filament.navigation.enable_badge')) {
            return (string) static::getModel()::count();
        }

        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (! (bool) config('filament-tracer.filament.navigation.enable_badge')) {
            return null;
        }

        $count = (int) static::getModel()::count();

        return match (true) {
            $count === 0 => 'success',
            $count < 10 => 'warning',
            default => 'danger',
        };
    }

    public static function getNavigationGroup(): string | UnitEnum | null
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

    public static function getNavigationIcon(): string | BackedEnum | null
    {
        return config('filament-tracer.filament.navigation.icon') ?? static::$navigationIcon;
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-tracer.filament.navigation.sort');
    }

    public static function canGloballySearch(): bool
    {
        return (bool) config('filament-tracer.filament.global_search', false);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['error_type', 'message', 'file'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('filament-tracer::labels.error_type') => class_basename((string) $record->error_type),
            __('filament-tracer::labels.file') => $record->short_location,
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return Str::limit((string) $record->message, 80);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }
}
