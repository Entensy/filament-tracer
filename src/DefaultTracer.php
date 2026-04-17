<?php

namespace Entensy\FilamentTracer;

use Entensy\FilamentTracer\Contracts\Tracerable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\LaravelIgnition\Recorders\QueryRecorder\QueryRecorder;
use Throwable;

class DefaultTracer implements Tracerable
{
    public function __construct(
        private Throwable $t,
        private ?Request $request = null,
    ) {
    }

    public static function make(Throwable $t): static
    {
        return new static($t);
    }

    public function getThrowable(): Throwable
    {
        return $this->t;
    }

    public function getSource(): string
    {
        return config('filament-tracer.source') ?? 'php';
    }

    public function getErrorType(): string
    {
        return get_class($this->getThrowable());
    }

    public function getPath(): string
    {
        if (function_exists('url')) {
            try {
                $full = url()->full();

                if (is_string($full) && $full !== '') {
                    return $full;
                }
            } catch (Throwable) {
                // Fall through to server-based reconstruction.
            }
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        return $host . $uri;
    }

    public function getIp(): string
    {
        if ($this->request) {
            return (string) $this->request->ip();
        }

        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function getCode(): string
    {
        return (string) $this->getThrowable()->getCode();
    }

    public function getMessage(): string
    {
        return $this->getThrowable()->getMessage();
    }

    public function getLine(): string
    {
        return (string) $this->getThrowable()->getLine();
    }

    public function getMethod(): string
    {
        return $this->getThrowable()->getTrace()[0]['function'] ?? '';
    }

    public function getFile(): string
    {
        return $this->getThrowable()->getFile();
    }

    public function getTraces(): string
    {
        return $this->getThrowable()->getTraceAsString();
    }

    public function getQueries(): string
    {
        if (! class_exists(QueryRecorder::class) || ! app()->bound(QueryRecorder::class)) {
            return '[]';
        }

        $queries = json_encode(app()->make(QueryRecorder::class)->getQueries());

        return $queries !== false ? $queries : '[]';
    }

    public function getBody(): string
    {
        if ($this->request) {
            $all = $this->request->all();

            if ($all !== []) {
                return (string) json_encode($all, JSON_UNESCAPED_SLASHES);
            }

            $content = $this->request->getContent();

            return $content !== '' ? $content : '{}';
        }

        if ($_POST === []) {
            return '{}';
        }

        return (string) json_encode($_POST, JSON_UNESCAPED_SLASHES);
    }

    public function getHeaders(): string
    {
        $headers = [];

        if ($this->request) {
            $headers = Arr::except($this->request->headers->all(), 'cookie');
        } else {
            foreach ($_SERVER as $key => $value) {
                $header = str($key);

                if ($header->startsWith('REQUEST_')) {
                    $headers[$header->toString()] = $value;

                    continue;
                }

                if ($header->startsWith('HTTP_')) {
                    $headers[$header->after('HTTP_')->toString()] = $value;
                }
            }
        }

        return (string) json_encode($headers, JSON_UNESCAPED_SLASHES);
    }

    public function getCookies(): string
    {
        if ($this->request) {
            return (string) json_encode($this->request->cookies->all(), JSON_UNESCAPED_SLASHES);
        }

        return $_SERVER['HTTP_COOKIE'] ?? '';
    }
}
