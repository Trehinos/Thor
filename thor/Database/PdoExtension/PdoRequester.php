<?php

namespace Thor\Database\PdoExtension;

use Thor\Thor;
use PDOStatement;

use PDOException;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;

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
    final public function execute(string $sql, array $parameters = []): bool
    {
        Logger::write("DB execute ($sql).");
        Logger::writeDebug('DB parameters', array_values($parameters), LogLevel::INFO);
        $stmt = $this->handler->getPdo()->prepare($sql);

        return $stmt->execute(array_values($parameters));
    }

    /**
     * Executes a parameterized SQL-query with the PdoHandler multiple times (one time for each array in $parameters).
     *
     * @param string  $sql
     * @param array[] $parameters
     * @param bool    $continueIfError
     *
     * @return bool
     */
    final public function executeMultiple(string $sql, array $parameters, bool $continueIfError = false): bool
    {
        $size = count($parameters);
        Logger::write("DB execute $size x ($sql).");
        $stmt = $this->handler->getPdo()->prepare($sql);
        $result = true;

        foreach ($parameters as $pdoRowsArray) {
            Logger::writeDebug(' -> DB parameters', array_values($pdoRowsArray), LogLevel::INFO);
            try {
                $result = $result && $stmt->execute(array_values($pdoRowsArray));
            } catch (PDOException $e) {
                if (!Thor::isDebug()) {
                    Logger::writeDebug(' -> DB parameters', array_values($pdoRowsArray), LogLevel::WARNING);
                }
                Logger::logThrowable($e->getMessage());
                Logger::writeDebug(' -> DB execution failed : ', array_values($pdoRowsArray), LogLevel::ERROR);
                if (!$continueIfError) {
                    throw $e;
                }
            }
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
    final public function request(string $sql, array $parameters = []): PDOStatement
    {
        Logger::write("DB request ($sql).");
        Logger::writeDebug('DB parameters', array_values($parameters), LogLevel::INFO);
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute(array_values($parameters));

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
