<?php

namespace JuNe\Process;
class Signal
{

    /**
     * @var callable[]
     */
    private array $connections;

    public function __construct(public string $name)
    {
    }

    public function connect(callable $func, mixed ...$args): callable
    {
        $this->connections[] = ['func' => $func, 'args' => $args ?? []];

        return $func;
    }

    public function disconnect(?callable $func = null): void
    {
        if ($func === null) {
            unset($this->connections);
            $this->connections = [];
            return;
        }
        foreach ($this->connections as $k => ['func' => $f]) {
            if ($f === $func) {
                $this->connections[$k] = null;
                unset($this->connections[$k]);
            }
        }

    }

    public function isConnected(?callable $func = null): bool
    {
        if ($func === null) {
            return !empty($this->connections);
        }
        foreach ($this->connections as $k => ['func' => $f]) {
            if ($f === $func) {
                return true;
            }
        }
        return false;
    }

    public function emit(mixed ...$args): void
    {
        foreach ($this->connections as $name => ['func' => $func, 'args' => $fargs]) {
            $func(...array_merge($fargs, $args ?? []));
        }
    }

}
