<?php

namespace Thor\Database\PdoExtension;

use PDOStatement;
use PDOException;
use Thor\Framework\Thor;
use Thor\Common\Debug\Logger;
use Thor\Common\Debug\LogLevel;

/**
 * Defines a class which performs SQL queries on a PDO connection wrapped in a PdoHandler.
 *
 * @package Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
class PdoRequester
{

    /**
     * @param PdoHandler $handler
     */
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
    public function execute(string $sql, array $parameters = []): bool
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
    public function executeMultiple(string $sql, array $parameters, bool $continueIfError = false): bool
    {
        $size = count($parameters);
        Logger::write("DB execute $size x ($sql).");
        $stmt = $this->handler->getPdo()->prepare($sql);
        $result = true;

        foreach ($parameters as $pdoRowsArray) {
            Logger::writeDebug(' -> DB parameters', array_values($pdoRowsArray), LogLevel::MINIMAL);
            try {
                $result = $result && $stmt->execute(array_values($pdoRowsArray));
            } catch (PDOException $e) {
                if (!Thor::isDebug()) {
                    Logger::writeDebug(' -> DB parameters', array_values($pdoRowsArray), LogLevel::WARNING);
                }
                Logger::writeDebug(' -> DB execution failed : ', array_values($pdoRowsArray), LogLevel::ERROR);
                Logger::logThrowable($e);
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
    public function request(string $sql, array $parameters = []): PDOStatement
    {
        Logger::write("DB request ($sql).");
        Logger::writeDebug('DB parameters', array_values($parameters), LogLevel::INFO);
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute(array_values($parameters));

        return $stmt;
    }

    /**
     * Returns this instance's PDO connection handler.
     */
    final public function getPdoHandler(): PdoHandler
    {
        return $this->handler;
    }

    /**
     * Format a string like "(?, ?, ?, ...)" where number of '?' is `count($elements)`
     *
     * @param array $elements
     *
     * @return string
     */
    public static function in(array $elements) : string
    {
        return '(' . implode(',', array_fill(0, count($elements), '?')) . ')';
    }

}
