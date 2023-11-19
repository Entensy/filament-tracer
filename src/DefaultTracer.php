<?php

namespace Entensy\FilamentTracer;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Entensy\FilamentTracer\Contracts\Tracerable;
use Spatie\LaravelIgnition\Recorders\QueryRecorder\QueryRecorder;

class DefaultTracer implements Tracerable
{
    public function __construct(
        private Throwable $t,
        private ?Request $request = null,
    ) {
    }

    /**
     * @return DefaultTracer
     */
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
        return url()->full() ?? ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '') . ($_SERVER['QUERY_STRING'] ?? '');
    }

    public function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function getCode(): string
    {
        return $this->getThrowable()->getCode();
    }

    public function getMessage(): string
    {
        return $this->getThrowable()->getMessage();
    }

    public function getLine(): string
    {
        return $this->getThrowable()->getLine();
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
        $queries = \json_encode(app()->make(QueryRecorder::class)->getQueries());

        return $queries ?? '';
    }

    public function getBody(): string
    {
        if ($this->request) {
            return $this->request->getContent();
        }

        if (count($_POST) === 0) {
            return '{}';
        }

        return \json_encode($_POST, JSON_UNESCAPED_SLASHES);
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

        $headers = \json_encode($headers, JSON_UNESCAPED_SLASHES);

        return $headers ?? '';
    }

    public function getCookies(): string
    {
        if ($this->request) {
            return \json_encode($this->request->cookies->all(), JSON_UNESCAPED_SLASHES);
        }

        return $_SERVER['HTTP_COOKIE'] ?? '';
    }
}
