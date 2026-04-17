<?php

namespace Entensy\FilamentTracer;

use Closure;
use Filament\Contracts\Plugin;
use Filament\FilamentManager;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;

class FilamentTracerPlugin implements Plugin
{
    use EvaluatesClosures;

    protected mixed $tracesCounterUsing = null;

    protected mixed $queriesCounterUsing = null;

    protected mixed $bodyCounterUsing = null;

    protected mixed $headersCounterUsing = null;

    protected mixed $cookiesCounterUsing = null;

    protected bool | Closure $hasStatsWidget = false;

    public function register(Panel $panel): void
    {
        $panel->resources([
            config('filament-tracer.filament.resource'),
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function getId(): string
    {
        return 'filament-tracer';
    }

    public static function filament(): FilamentManager | FilamentTracerPlugin
    {
        return filament('filament-tracer');
    }

    public static function make(): static
    {
        return new self;
    }

    public function tracesCounterUsing(mixed $callback): static
    {
        $this->tracesCounterUsing = $callback;

        return $this;
    }

    public function getTracesCounter(Model $record): int
    {
        if ($this->tracesCounterUsing !== null) {
            return (int) $this->evaluate($this->tracesCounterUsing, ['record' => $record]);
        }

        return count($record->traces_array ?? []);
    }

    public function queriesCounterUsing(mixed $callback): static
    {
        $this->queriesCounterUsing = $callback;

        return $this;
    }

    public function getQueriesCounter(Model $record): int
    {
        if ($this->queriesCounterUsing !== null) {
            return (int) $this->evaluate($this->queriesCounterUsing, ['record' => $record]);
        }

        return count((array) ($record->queries ?? []));
    }

    public function bodyCounterUsing(mixed $callback): static
    {
        $this->bodyCounterUsing = $callback;

        return $this;
    }

    public function getBodyCounter(Model $record): int
    {
        if ($this->bodyCounterUsing !== null) {
            return (int) $this->evaluate($this->bodyCounterUsing, ['record' => $record]);
        }

        return count((array) ($record->body ?? []));
    }

    public function headersCounterUsing(mixed $callback): static
    {
        $this->headersCounterUsing = $callback;

        return $this;
    }

    public function getHeadersCounter(Model $record): int
    {
        if ($this->headersCounterUsing !== null) {
            return (int) $this->evaluate($this->headersCounterUsing, ['record' => $record]);
        }

        return count((array) ($record->headers ?? []));
    }

    public function cookiesCounterUsing(mixed $callback): static
    {
        $this->cookiesCounterUsing = $callback;

        return $this;
    }

    public function getCookiesCounter(Model $record): int
    {
        if ($this->cookiesCounterUsing !== null) {
            return (int) $this->evaluate($this->cookiesCounterUsing, ['record' => $record]);
        }

        return count((array) ($record->visible_cookies ?? []));
    }

    /**
     * Opt in to the "at a glance" stats widget on the tracer list page.
     */
    public function statsWidget(bool | Closure $condition = true): static
    {
        $this->hasStatsWidget = $condition;

        return $this;
    }

    public function hasStatsWidget(): bool
    {
        return (bool) $this->evaluate($this->hasStatsWidget);
    }
}
