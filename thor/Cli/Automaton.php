<?php

namespace Thor\Cli;

abstract class Automaton extends Daemon
{

    final public function execute(): void
    {
        $this->init();
        $collection = $this->load();
        $this->before();
        foreach ($collection as $item) {
            if (!$this->one($item)) {
                break;
            }
        }
        $this->after();
    }

    public function init(): void
    {
    }

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    abstract public function load(): array;

    abstract public function one(mixed $item): bool;

}
