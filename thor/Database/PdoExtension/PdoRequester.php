<?php

namespace Thor\Database\PdoExtension;

use PDOStatement;

use Thor\Debug\Logger;

/**
 * Defines a class which performs SQL queries on a PDO connection wrapped in a PdoHandler.
 *
 * @package Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
class PdoRequester
{

    public function __construct(protected PdoHandler $handler)
    {
    }

    /**
     * Executes a parameterized SQL-query with the PdoHandler.
     *
     * @param string $sql
     * @param array  $parameters
     *
     * @return bool
     */
    final public function execute(string $sql, array $parameters): bool
    {
        Logger::write("DB execute ($sql).");
        Logger::writeData('DB parameters', $parameters);
        $stmt = $this->handler->getPdo()->prepare($sql);

        return $stmt->execute($parameters);
    }

    /**
     * Executes a parameterized SQL-query with the PdoHandler multiple times (one time for each array in $parameters).
     *
     * @param string $sql
     * @param array[] $parameters
     *
     * @return bool
     */
    final public function executeMultiple(string $sql, array $parameters): bool
    {
        $size = count($parameters);
        Logger::write("DB execute $size x ($sql).");
        $stmt = $this->handler->getPdo()->prepare($sql);
        $result = true;

        foreach ($parameters as $pdoRowsArray) {
            Logger::writeData(' -> DB parameters', $pdoRowsArray);
            $result = $result && $stmt->execute($pdoRowsArray);
        }

        return $result;
    }

    /**
     * Executes a parameterized SQL-query with the PdoHandler and returns the result as a PDOStatement object.
     *
     * @param string $sql
     * @param array $parameters
     *
     * @return PDOStatement
     */
    final public function request(string $sql, array $parameters): PDOStatement
    {
        Logger::write("DB request ($sql).");
        Logger::writeData('DB parameters', $parameters);
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute($parameters);

        return $stmt;
    }

    /**
     * Gets the PdoHandler of this requester.
     */
    final public function getPdoHandler(): PdoHandler
    {
        return $this->handler;
    }

}
