<?php

namespace Entensy\FilamentTracer\Models;

use Entensy\FilamentTracer\Enums\Severity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Tracer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'queries' => 'array',
        'body' => 'array',
        'headers' => 'array',
        'cookies' => 'array',
    ];

    public function getTable()
    {
        return config('filament-tracer.database.table_name');
    }

    public function getKeyName()
    {
        return config('filament-tracer.database.primary_key');
    }

    /**
     * Return the stored stack trace as an array of lines.
     *
     * @return array<int, string>
     */
    public function getTracesArrayAttribute(): array
    {
        $traces = (string) ($this->attributes['traces'] ?? '');

        if ($traces === '') {
            return [];
        }

        return preg_split('/\r\n|\r|\n/', $traces) ?: [];
    }

    /**
     * Return cookies, optionally stripping null/empty values per config.
     *
     * @return array<string, mixed>
     */
    public function getVisibleCookiesAttribute(): array
    {
        $cookies = \json_decode((string) $this->cookies, associative: true) ?? [];

        if (config('filament-tracer.filament.cookies.hide_null_values')) {
            $cookies = Arr::where(
                $cookies,
                fn ($value) => $value !== null && $value !== '',
            );
        }

        return $cookies;
    }

    /**
     * Derived severity for this error based on the stored error_type.
     */
    public function getSeverityAttribute(): Severity
    {
        return Severity::fromErrorType($this->attributes['error_type'] ?? null);
    }

    /**
     * Short filename + line, e.g. `Handler.php:42`, for display purposes.
     */
    public function getShortLocationAttribute(): string
    {
        $file = (string) ($this->attributes['file'] ?? '');
        $line = $this->attributes['line'] ?? null;

        $basename = $file !== '' ? basename($file) : '';

        if ($basename === '') {
            return '';
        }

        return $line !== null && $line !== ''
            ? "{$basename}:{$line}"
            : $basename;
    }

    /**
     * Build a Markdown bug report you can paste into an issue tracker.
     */
    public function toMarkdownReport(): string
    {
        $lines = [];

        $lines[] = '# ' . ($this->error_type ?? 'Error');
        $lines[] = '';
        $lines[] = '> ' . Str::of((string) $this->message)->replaceMatches('/\R/', ' ')->limit(500);
        $lines[] = '';
        $lines[] = '| | |';
        $lines[] = '| --- | --- |';
        $lines[] = '| Source | `' . ($this->source ?? '—') . '` |';
        $lines[] = '| Code | `' . ($this->code ?? '—') . '` |';
        $lines[] = '| File | `' . ($this->file ?? '—') . ':' . ($this->line ?? '—') . '` |';
        $lines[] = '| Method | `' . ($this->method ?? '—') . '` |';
        $lines[] = '| IP | `' . ($this->ip ?? '—') . '` |';
        $lines[] = '| Path | `' . ($this->path ?? '—') . '` |';
        $lines[] = '| When | `' . optional($this->created_at)->toDateTimeString() . '` |';
        $lines[] = '';

        $traces = (string) ($this->attributes['traces'] ?? '');

        if ($traces !== '') {
            $lines[] = '## Stack trace';
            $lines[] = '';
            $lines[] = '```';
            $lines[] = $traces;
            $lines[] = '```';
            $lines[] = '';
        }

        $queries = (array) ($this->queries ?? []);

        if ($queries !== []) {
            $lines[] = '## Queries (' . count($queries) . ')';
            $lines[] = '';
            foreach ($queries as $q) {
                $q = (array) $q;
                $sql = self::interpolateSql((string) ($q['sql'] ?? ''), (array) ($q['bindings'] ?? []));
                $lines[] = '- `' . ($q['connection_name'] ?? '—') . '` · `' . ($q['time'] ?? '—') . ' ms`';
                $lines[] = '  ```sql';
                $lines[] = '  ' . $sql;
                $lines[] = '  ```';
            }
            $lines[] = '';
        }

        $body = (array) ($this->body ?? []);

        if ($body !== []) {
            $lines[] = '## Body';
            $lines[] = '';
            $lines[] = '```json';
            $lines[] = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $lines[] = '```';
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    /**
     * Interpolate positional bindings into a single SQL query string.
     *
     * @param  array<int, mixed>  $bindings
     */
    public static function interpolateSql(string $sql, array $bindings): string
    {
        if ($bindings === []) {
            return $sql;
        }

        $index = 0;

        return (string) preg_replace_callback(
            '/\?/',
            function () use ($bindings, &$index) {
                if (! array_key_exists($index, $bindings)) {
                    return '?';
                }

                $value = $bindings[$index++];

                if ($value === null) {
                    return 'NULL';
                }

                if (is_bool($value)) {
                    return $value ? '1' : '0';
                }

                if (is_numeric($value)) {
                    return (string) $value;
                }

                return "'" . str_replace("'", "''", (string) $value) . "'";
            },
            $sql,
        );
    }
}
