<?php

namespace Entensy\FilamentTracer\Concerns;

trait HasSourceExpectation
{
    /**
     * Check source contains given expected string value
     */
    public function doesSourceContain(string $expected): bool
    {
        $record = $this->getRecord();
        $src = strtolower($record->source);

        return strpos($src, $expected) !== -1;
    }
}
