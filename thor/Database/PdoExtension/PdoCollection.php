<?php

/**
 * @package Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoExtension;

use PDO;

final class PdoCollection
{

    private array $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function add(string $connectionName, PdoHandler $handler): void
    {
        $this->handlers[$connectionName] = $handler;
    }

    public function get(string $connectionName = 'default'): ?PdoHandler
    {
        return $this->handlers[$connectionName] ?? null;
    }

    public function all(): array
    {
        return $this->handlers;
    }

    public static function createFromConfiguration(array $db_config): self
    {
        $pdos = new self();

        foreach ($db_config as $connectionName => $config) {
            $pdos->add(
                $connectionName,
                new PdoHandler(
                    $config['dsn'] ?? '',
                    $config['user'] ?? '',
                    $config['password'] ?? '',
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
