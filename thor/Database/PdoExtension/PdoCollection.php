<?php

namespace Thor\Database\PdoExtension;

use PDO;

/**
 * Holds a collection of PdoHandlers.
 *
 * @see PdoHandler
 *
 * @package Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
final class PdoCollection
{

    /**
     * @var PdoHandler[]
     */
    private array $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    /**
     * Adds a new PdoHandler in the collection.
     *
     * Fluent method.
     */
    public function add(string $connectionName, PdoHandler $handler): self
    {
        $this->handlers[$connectionName] = $handler;
        return $this;
    }

    /**
     * Gets a PdoHandler from its name.
     *
     * If the PdoHandler is not found in the collection, this method returns null.
     */
    public function get(string $connectionName = 'default'): ?PdoHandler
    {
        return $this->handlers[$connectionName] ?? null;
    }

    /**
     * Gets all PdoHandlers in this collection.
     *
     * @return PdoHandler[]
     */
    public function all(): array
    {
        return $this->handlers;
    }

    /**
     * Creates the PdoCollection from a configuration array.
     */
    public static function createFromConfiguration(array $db_config): self
    {
        $pdos = new self();

        foreach ($db_config as $connectionName => $config) {
            $pdos->add(
                $connectionName,
                new PdoHandler(
                    $config['dsn'] ?? '',
                    $config['user'] ?? null,
                    $config['password'] ?? null,
                    match (strtolower($config['case'] ?? 'natural')) {
                        'upper' => PDO::CASE_UPPER,
                        'lower' => PDO::CASE_LOWER,
                        default => PDO::CASE_NATURAL
                    },
                )
            );
        }

        return $pdos;
    }

}
