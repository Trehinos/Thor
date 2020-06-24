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

    public function execute(string $sql, array $parameters)
    {
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute($parameters);
    }

    public function request(string $sql, array $parameters): PDOStatement
    {
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute($parameters);

        return $stmt;
    }

}
