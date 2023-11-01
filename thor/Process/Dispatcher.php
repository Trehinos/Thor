<?php

namespace Thor\Process;

use Thor\Tools\Guid;

class Dispatcher
{

    private array $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function on(string $event, callable $callback, ?string $id = null): string
    {
        if (!array_key_exists($event, $this->events)) {
            $this->events[$event] = [];
        }
        $id ??= Guid::hex(4);
        $this->events[$event][$id] = $callback;

        return $id;
    }

    public function off(string $event, ?string $id = null): void
    {
        if ($id === null) {
            $this->events[$event] = [];
            return;
        }
        $this->events[$event][$id] = null;
        unset($this->events[$event][$id]);
    }

    public function trigger(string $event, mixed ...$data): void
    {
        foreach (($this->events[$event] ?? []) as $callback) {
            $callback(...$data);
        }
    }

}
