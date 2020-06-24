<?php

namespace Thor\Database;

use PDOStatement;

final class PdoRequester
{

    private PdoHandler $handler;

    public function __construct(PdoHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * execute
     *      Execute a parameterized SQL query with the PdoHandler
     *
     * @param string $sql
     * @param array $parameters
     */
    public function execute(string $sql, array $parameters)
    {
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute($parameters);
    }

    /**
     * request
     *      Execute a parameterized SQL query with the PdoHandler and returns the result as a PDOStatement object.
     *
     * @param string $sql
     * @param array $parameters
     * @return PDOStatement
     */
    public function request(string $sql, array $parameters): PDOStatement
    {
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute($parameters);

        return $stmt;
    }

}
