<?php

namespace Entensy\FilamentTracer\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Reactive;

class TracerStatsOverviewWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    /**
     * Reactive mirror of the host page's `tableFilters` state.
     *
     * Populated automatically when the page uses the
     * `ExposesTableToWidgets` trait (see ListTracers).
     *
     * @var array<string, mixed> | null
     */
    #[Reactive]
    public ?array $tableFilters = null;

    protected function getStats(): array
    {
        /** @var class-string<Model> $model */
        $model = config('filament-tracer.model');

        $total = $this->applyFilters($model::query())->count();
        $today = $this->applyFilters($model::query())->whereDate('created_at', today())->count();
        $weekly = $this->applyFilters($model::query())->where('created_at', '>=', now()->subDays(7))->count();

        $topError = $this->applyFilters($model::query())
            ->selectRaw('error_type, COUNT(*) as aggregate')
            ->groupBy('error_type')
            ->orderByDesc('aggregate')
            ->limit(1)
            ->first();

        $chart = $this->buildWeeklyChart($model);

        return [
            Stat::make(__('filament-tracer::labels.widget.total'), $total)
                ->description(__('filament-tracer::labels.widget.total_description'))
                ->descriptionIcon('heroicon-o-circle-stack')
                ->color($total > 0 ? 'gray' : 'success'),

            Stat::make(__('filament-tracer::labels.widget.today'), $today)
                ->description(__('filament-tracer::labels.widget.today_description'))
                ->descriptionIcon('heroicon-o-bolt')
                ->color($today === 0 ? 'success' : ($today > 10 ? 'danger' : 'warning')),

            Stat::make(__('filament-tracer::labels.widget.week'), $weekly)
                ->description(__('filament-tracer::labels.widget.week_description'))
                ->descriptionIcon('heroicon-o-calendar-days')
                ->chart($chart)
                ->color($weekly === 0 ? 'success' : ($weekly > 50 ? 'danger' : 'warning')),

            Stat::make(
                __('filament-tracer::labels.widget.top_error'),
                $topError ? class_basename((string) $topError->error_type) : '—',
            )
                ->description($topError ? ($topError->aggregate . ' ' . __('filament-tracer::labels.widget.occurrences')) : __('filament-tracer::labels.widget.no_errors'))
                ->descriptionIcon('heroicon-o-fire')
                ->color($topError ? 'danger' : 'success'),
        ];
    }

    /**
     * Apply the active page filters to the given query.
     *
     * Supported filter keys: `created_at.created_from`, `created_at.created_until`,
     * `error_type.value`, `source.value`.
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    protected function applyFilters(Builder $query): Builder
    {
        $filters = $this->tableFilters ?? [];

        $createdFrom = data_get($filters, 'created_at.created_from');
        $createdUntil = data_get($filters, 'created_at.created_until');
        $errorType = data_get($filters, 'error_type.value');
        $source = data_get($filters, 'source.value');

        return $query
            ->when($createdFrom, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($createdUntil, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))
            ->when($errorType, fn (Builder $q, $value) => $q->where('error_type', $value))
            ->when($source, fn (Builder $q, $value) => $q->where('source', $value));
    }

    /**
     * @param  class-string<Model>  $model
     * @return array<int, int>
     */
    protected function buildWeeklyChart(string $model): array
    {
        $counts = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();

            $counts[] = $this->applyFilters($model::query())
                ->whereDate('created_at', $day)
                ->count();
        }

        return $counts;
    }
}
