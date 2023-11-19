<?php

namespace Entensy\FilamentTracer\Concerns;

use Error;

trait CanChangeState
{
    protected mixed $changeStateUsing = null;

    public function changeStateUsing(mixed $callback): static
    {
        $this->changeStateUsing = $callback;

        return $this;
    }

    public function getChangeState(): mixed
    {
        if ($this->changeStateUsing !== null) {
            return $this->evaluate($this->changeStateUsing);
        }

        if (! method_exists($this, 'getDefaultChangeState')) {
            $instance = get_class($this);

            throw new Error("'getDefaultChangeState' does not exist in entry component of type '{$instance}'!");
        }

        return $this->getDefaultChangeState();
    }
}
