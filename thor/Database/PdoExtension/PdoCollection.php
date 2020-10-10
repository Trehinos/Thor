<?php

namespace Thor\Database\PdoExtension;

/**
 * Class PdoCollection: a collection of PdoHandlers
 * @package Thor\Database\PdoExtension
 *
 * @since 2020-06
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
final class PdoCollection
{

    /**
     * @var PdoHandler[]
     */
    private array $handlers = [];

    /**
     * add(): add a PdoHandler to the collection.
     *
     * @param string $connectionName
     * @param PdoHandler $handler
     */
    public function add(string $connectionName, PdoHandler $handler)
    {
        $this->handlers[$connectionName] = $handler;
    }

    /**
     * get(): returns a PdoHandler identified by the name.
     *
     * @param string $connectionName
     *
     * @return PdoHandler|null
     */
    public function get(string $connectionName): ?PdoHandler
    {
        return $this->handlers[$connectionName] ?? null;
    }

    /**
     * all(): returns all PdoHandlers of the collection.
     *
     * @return PdoHandler[]
     */
    public function all(): array
    {
        return $this->handlers;
    }

}
