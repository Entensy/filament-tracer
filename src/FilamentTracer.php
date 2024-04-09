<?php

namespace Entensy\FilamentTracer;

use Error;
use Throwable;
use Illuminate\Http\Request;
use Entensy\FilamentTracer\Contracts\HasStore;
use Entensy\FilamentTracer\Contracts\Tracerable;

class FilamentTracer
{
    public static function capture(Throwable $t, ?Request $request = null): bool
    {
        /** @var \Entensy\FilamentTracer\Models\Tracer $model */
        $model = static::getModel();

        $excepts = static::getExceptList();

        foreach ($excepts as $except) {
            if (is_a($t, $except, true)) {
                return false;
            }
        }

        $tracerInstance = static::getTracer();

        if (! (is_a($tracerInstance, Tracerable::class, true))) {
            throw new Error("Custom tracer class '{$tracerInstance}' has to implement 'Tracerable' interface!");

            return false;
        }

        /** @var \Entensy\FilamentTracer\Contracts\Tracerable $tracer */
        $tracer = new $tracerInstance($t, $request);

        $data = [
            'source' => $tracer->getSource(),
            'error_type' => $tracer->getErrorType(),
            'ip' => $tracer->getIp(),
            'path' => $tracer->getPath(),
            'code' => $tracer->getCode(),
            'message' => $tracer->getMessage(),
            'line' => $tracer->getLine(),
            'method' => $tracer->getMethod(),
            'file' => $tracer->getFile(),
            'traces' => $tracer->getTraces(),
            'queries' => $tracer->getQueries(),
            'body' => $tracer->getBody(),
            'headers' => $tracer->getHeaders(),
            'cookies' => $tracer->getCookies(),
            'created_at' => time(),
        ];

        try {
            if ($tracer instanceof HasStore) {
                $tracer->store();
            } else {
                $model::create($data);
            }

            return true;
        } catch (Throwable $ex) {
            return false;
        }
    }

    public static function getModel(): string
    {
        return config('filament-tracer.model');
    }

    public static function getTracer(): string
    {
        return config('filament-tracer.tracer');
    }

    public static function getExceptList(): array
    {
        return config('filament-tracer.except');
    }
}
