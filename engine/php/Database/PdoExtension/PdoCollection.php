<?php

namespace Thor\Database\PdoExtension;

final class PdoCollection
{

    private array $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function add(string $connectionName, PdoHandler $handler)
    {
        $this->handlers[$connectionName] = $handler;
    }

    public function get(string $connectionName): ?PdoHandler
    {
        return $this->handlers[$connectionName] ?? null;
    }

    public function all(): array
    {
        return $this->handlers;
    }

}
