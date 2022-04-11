<?php

namespace Thor\Database\PdoExtension;

use PDO;
use Thor\Configuration\Configuration;
use Thor\Structures\Collection\Collection;
use Thor\Configuration\DatabasesConfiguration;

/**
 * Holds a collection of PdoHandlers.
 *
 * @see PdoHandler
 *
 * @package Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
final class PdoCollection extends Collection
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Adds a new PdoHandler in the collection.
     *
     * Fluent method.
     */
    public function add(string $connectionName, PdoHandler $handler): self
    {
        $this[$connectionName] = $handler;
        return $this;
    }

    /**
     * Gets a PdoHandler from its name.
     *
     * If the PdoHandler is not found in the collection, this method returns null.
     */
    public function get(string $connectionName = 'default'): ?PdoHandler
    {
        return $this[$connectionName] ?? null;
    }

    /**
     * Gets all PdoHandlers in this collection.
     *
     * @return PdoHandler[]
     */
    public function all(): array
    {
        return $this->toArray();
    }

    /**
     * Creates the PdoCollection from a configuration array.
     */
    public static function createFromConfiguration(DatabasesConfiguration $db_config): self
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
