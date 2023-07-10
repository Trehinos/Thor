<?php

namespace Thor\Framework\Configurations;

use PDO;
use Thor\Configuration\Configuration;
use Thor\Configuration\ConfigurationFromFile;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoExtension\PdoCollection;

final class DatabasesConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('database');
    }

    /**
     * @return Configuration
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
                $this->createPdoHandler($connectionName)
            );
        }

        return $pdos;
    }

    public function createPdoRequester(string $name): ?PdoRequester
    {
        $config = $this[$name] ?? null;
        if ($config === null) {
            return null;
        }

        return new PdoRequester($this->createPdoHandler($name));
    }

    public function createPdoHandler(string $name): ?PdoHandler
    {
        $config = $this[$name] ?? null;
        if ($config === null) {
            return null;
        }

        return new PdoHandler(
            $config['dsn'] ?? '',
            $config['user'] ?? null,
            $config['password'] ?? null,
            match (strtolower($config['case'] ?? 'natural')) {
                'upper' => PDO::CASE_UPPER,
                'lower' => PDO::CASE_LOWER,
                default => PDO::CASE_NATURAL
            },
        );
    }

}
