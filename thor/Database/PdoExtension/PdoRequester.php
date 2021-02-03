<?php

namespace Thor\Database\PdoExtension;

use PDOStatement;

use Thor\Debug\Logger;

final class PdoRequester
{

    public function __construct(private PdoHandler $handler)
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
    public function execute(string $sql, array $parameters): bool
    {
        Logger::write("DB execute ($sql).", Logger::LEVEL_DEBUG);
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
    public function executeMultiple(string $sql, array $parameters): bool
    {
        $size = count($parameters);
        Logger::write("DB execute $size x ($sql).", Logger::LEVEL_DEBUG);
        $stmt = $this->handler->getPdo()->prepare($sql);
        $result = true;

        foreach ($parameters as $pdoRowsArray) {
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
    public function request(string $sql, array $parameters): PDOStatement
    {
        Logger::write("DB request ($sql).", Logger::LEVEL_DEBUG);
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute($parameters);

        return $stmt;
    }

    public function getPdoHandler(): PdoHandler
    {
        return $this->handler;
    }

}
