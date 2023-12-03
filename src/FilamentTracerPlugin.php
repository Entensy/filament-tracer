<?php

namespace Entensy\FilamentTracer;

use Filament\Panel;
use Filament\FilamentManager;
use Filament\Contracts\Plugin;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class FilamentTracerPlugin implements Plugin
{
    use EvaluatesClosures;

    protected mixed $tracesCounterUsing = null;

    protected mixed $queriesCounterUsing = null;

    protected mixed $bodyCounterUsing = null;

    protected mixed $headersCounterUsing = null;

    protected mixed $cookiesCounterUsing = null;

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

    public static function filament(): FilamentManager|FilamentTracerPlugin
    {
        return filament('filament-tracer');
    }

    public static function make(): static
    {
        return new static;
    }

    public function tracesCounterUsing(mixed $callback): static
    {
        $this->tracesCounterUsing = $callback;

        return $this;
    }

    public function getTracesCounter(Model $record): int
    {
        if ($this->tracesCounterUsing !== null) {
            return $this->evaluate($this->tracesCounterUsing, ['record' => $record]);
        }

        // Traces should be a text based with new lines
        $traces = explode(PHP_EOL, $record->traces);

        return count($traces);
    }

    public function queriesCounterUsing(mixed $callback): static
    {
        $this->queriesCounterUsing = $callback;

        return $this;
    }

    public function getQueriesCounter(Model $record): int
    {
        if ($this->queriesCounterUsing !== null) {
            return $this->evaluate($this->queriesCounterUsing, ['record' => $record]);
        }

        $queries = \json_decode($record->queries, associative: true);

        return is_array($queries) ? count($queries) : strlen($queries);
    }

    public function bodyCounterUsing(mixed $callback): static
    {
        $this->bodyCounterUsing = $callback;

        return $this;
    }

    public function getBodyCounter(Model $record): int
    {
        if ($this->bodyCounterUsing !== null) {
            return $this->evaluate($this->bodyCounterUsing, ['record' => $record]);
        }

        $body = \json_decode($record->body, associative: true);

        return $body ? count($body) : 0;
    }

    public function headersCounterUsing(mixed $callback): static
    {
        $this->headersCounterUsing = $callback;

        return $this;
    }

    public function getHeadersCounter(Model $record): int
    {
        if ($this->headersCounterUsing !== null) {
            return $this->evaluate($this->headersCounterUsing, ['record' => $record]);
        }

        $headers = \json_decode($record->headers, associative: true);

        return is_array($headers) ? count($headers) : strlen($headers);
    }

    public function cookiesCounterUsing(mixed $callback): static
    {
        $this->cookiesCounterUsing = $callback;

        return $this;
    }

    public function getCookiesCounter(Model $record): int
    {
        if ($this->cookiesCounterUsing !== null) {
            return $this->evaluate($this->cookiesCounterUsing, ['record' => $record]);
        }

        $cookies = \json_decode($record->cookies, associative: true);

        if (config('filament-tracer.filament.cookies.hide_null_values')) {
            $cookies = Arr::whereNotNull($cookies);
        }

        return is_array($cookies) ? count($cookies) : strlen($cookies);
    }
}
