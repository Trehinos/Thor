<?php

namespace Thor\Process;

class Dispatcher
{

    private array $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function addListener(string $event, callable $callback): void
    {
        if (!array_key_exists($event, $this->events)) {
            $this->events[$event] = [];
        }
        $this->events[$event][] = $callback;
    }

    public function trigger(string $event, mixed ...$data): void
    {
        foreach (($this->events[$event] ?? []) as $callback) {
            $callback(...$data);
        }
    }

}
