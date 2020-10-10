<?php

namespace Thor\Database\PdoExtension;

use PDOStatement;

use Thor\Debug\Logger;

/**
 * Class PdoRequester: use a PdoHandler to request a database.
 * @package Thor\Database\PdoExtension
 *
 * @since 2020-06
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
final class PdoRequester
{

    private PdoHandler $handler;

    /**
     * PdoRequester constructor.
     *
     * @param PdoHandler $handler
     */
    public function __construct(PdoHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * execute(): execute a parameterized SQL query with the PdoHandler
     *
     * @param string $sql
     * @param array $parameters
     *
     * @return bool if the request is a success.
     */
    public function execute(string $sql, array $parameters = []): bool
    {
        Logger::write("DB execute ($sql).", Logger::DEBUG);
        $stmt = $this->handler->getPdo()->prepare($sql);

        return $stmt->execute($parameters);
    }

    /**
     * request(): execute a parameterized SQL query with the PdoHandler and returns the result as a PDOStatement object.
     *
     * @param string $sql
     * @param array $parameters
     *
     * @return PDOStatement as a result.
     */
    public function request(string $sql, array $parameters = []): PDOStatement
    {
        Logger::write("DB request ($sql).", Logger::DEBUG);
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute($parameters);

        return $stmt;
    }

}
