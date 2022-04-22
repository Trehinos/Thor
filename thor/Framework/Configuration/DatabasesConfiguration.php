<?php

namespace Thor\Framework\Configuration;

use PDO;
use Thor\Configuration\Configuration;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Configuration\ConfigurationFromFile;

final class DatabasesConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('database');
    }

    public function getDefault(): Configuration
    {
        return new Configuration($this['default'] ?? []);
    }

    public function getConfigurationOf(string $name): ?Configuration
    {
        return ($this[$name] ?? null) ? new Configuration($this[$name]) : null;
    }

    /**
     * Creates the PdoCollection from a configuration array.
     */
    public function createPdoCollection(): PdoCollection
    {
        $pdos = new PdoCollection();

        foreach ($this->getArrayCopy() as $connectionName => $config) {
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
