<?php

/**
 * @package Trehinos/Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoExtension;

use PDOStatement;

use Thor\Debug\Logger;
use Thor\Debug\LogLevel;

class PdoRequester
{

    public function __construct(protected PdoHandler $handler)
    {
    }

    /**
     * execute
     *      Execute a parameterized SQL query with the PdoHandler
     *
     * @param string $sql
     * @param array $parameters
     *
     * @return bool
     */
    final public function execute(string $sql, array $parameters): bool
    {
        Logger::write("DB execute ($sql).", LogLevel::DEBUG);
        Logger::writeData('DB parameters', $parameters, LogLevel::DEBUG);
        $stmt = $this->handler->getPdo()->prepare($sql);

        return $stmt->execute($parameters);
    }

    /**
     * executeMultiple
     *      Execute a parameterized SQL query with the PdoHandler
     *
     * @param string $sql
     * @param array[] $parameters
     *
     * @return bool
     */
    final public function executeMultiple(string $sql, array $parameters): bool
    {
        $size = count($parameters);
        Logger::write("DB execute $size x ($sql).", LogLevel::DEBUG);
        $stmt = $this->handler->getPdo()->prepare($sql);
        $result = true;

        foreach ($parameters as $pdoRowsArray) {
            Logger::writeData(' -> DB parameters', $pdoRowsArray, LogLevel::DEBUG);
            $result = $result && $stmt->execute($pdoRowsArray);
        }

        return $result;
    }

    /**
     * request
     *      Execute a parameterized SQL query with the PdoHandler and returns the result as a PDOStatement object.
     *
     * @param string $sql
     * @param array $parameters
     *
     * @return PDOStatement
     */
    final public function request(string $sql, array $parameters): PDOStatement
    {
        Logger::write("DB request ($sql).", LogLevel::DEBUG);
        Logger::writeData('DB parameters', $parameters, LogLevel::DEBUG);
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute($parameters);

        return $stmt;
    }

    final public function getPdoHandler(): PdoHandler
    {
        return $this->handler;
    }

}
