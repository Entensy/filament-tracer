<?php

namespace Entensy\FilamentTracer\Contracts;

use Throwable;
use Illuminate\Http\Request;

interface Tracerable
{
    public function __construct(Throwable $t, ?Request $request = null);

    public function getThrowable(): Throwable;

    public function getSource(): string;

    public function getErrorType(): string;

    public function getPath(): string;

    public function getIp(): string;

    public function getCode(): string;

    public function getMessage(): string;

    public function getLine(): string;

    public function getFile(): string;

    public function getMethod(): string;

    public function getTraces(): string;

    public function getQueries(): string;

    public function getBody(): string;

    public function getHeaders(): string;

    public function getCookies(): string;
}
