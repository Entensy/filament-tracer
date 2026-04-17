<?php

namespace Entensy\FilamentTracer\Enums;

use Illuminate\Support\Str;

enum Severity: string
{
    case Critical = 'critical';
    case Error = 'error';
    case Warning = 'warning';
    case Notice = 'notice';

    /**
     * Infer severity from an exception class name. We match on well known
     * Laravel / PHP base types first, then fall back to generic Error/Exception.
     */
    public static function fromErrorType(?string $errorType): self
    {
        if ($errorType === null || $errorType === '') {
            return self::Error;
        }

        $normalized = Str::lower($errorType);

        foreach (self::criticalNeedles() as $needle) {
            if (Str::contains($normalized, $needle)) {
                return self::Critical;
            }
        }

        foreach (self::noticeNeedles() as $needle) {
            if (Str::contains($normalized, $needle)) {
                return self::Notice;
            }
        }

        foreach (self::warningNeedles() as $needle) {
            if (Str::contains($normalized, $needle)) {
                return self::Warning;
            }
        }

        return self::Error;
    }

    /**
     * Filament color token to use for this severity.
     */
    public function color(): string
    {
        return match ($this) {
            self::Critical => 'danger',
            self::Error => 'danger',
            self::Warning => 'warning',
            self::Notice => 'info',
        };
    }

    /**
     * Heroicon name to show for this severity.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Critical => 'heroicon-o-fire',
            self::Error => 'heroicon-o-exclamation-triangle',
            self::Warning => 'heroicon-o-exclamation-circle',
            self::Notice => 'heroicon-o-information-circle',
        };
    }

    public function label(): string
    {
        return __('filament-tracer::labels.severity.' . $this->value);
    }

    /**
     * @return array<int, string>
     */
    private static function criticalNeedles(): array
    {
        return [
            'fatal',
            'outofmemory',
            'parse',
            'syntax',
            'typeerror',
            'assertionerror',
            'databaseerror',
            'connectionexception',
            'querycrashed',
            'queryexception',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function warningNeedles(): array
    {
        return [
            'validation',
            'authorization',
            'authentication',
            'throttle',
            'toomanyrequests',
            'httpexception',
            'modelnotfound',
            'notfound',
            'methodnotallowed',
            'unauthorized',
            'forbidden',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function noticeNeedles(): array
    {
        return [
            'deprecation',
            'notice',
            'info',
        ];
    }
}
