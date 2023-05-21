<?php

namespace Thor\Framework\Configurations;

use PDO;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Common\Configuration\Configuration;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Common\Configuration\ConfigurationFromFile;

final class DatabasesConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('database');
    }

    /**
     * @return \Thor\Common\Configuration\Configuration
     */
    public function getDefault(): Configuration
    {
        return new Configuration($this['default'] ?? []);
    }

    /**
     * @param string $name
     *
     * @return Configuration|null
     */
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
