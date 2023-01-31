<?php

namespace Evolution\Common\Action;

abstract class BaseAction implements Action
{

    private float $completion = 0;

    public function __construct(
        private ActionType $type,
        private string     $name,
        private float        $duration = 1,
    ) {
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function getType(): ActionType
    {
        return $this->type;
    }

    final public function getDuration(): float
    {
        return $this->duration;
    }

    final public function isComplete(): bool
    {
        return $this->completion >= $this->duration;
    }

    final public function getCompletion(): float
    {
        return $this->completion / $this->duration;
    }

    final public function advance(): void
    {
        $this->beforeTour();
        $this->completion += $this->tour();
        $this->afterTour();
        if ($this->isComplete()) {
            $this->onComplete();
        }
    }

    /**
     * Does nothing by default.
     *
     * @return void
     */
    public function onComplete(): void
    {
    }
    public function beforeTour(): void
    {
    }
    public function afterTour(): void
    {
    }

    abstract public function tour(): float;

}
