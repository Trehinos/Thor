<?php

namespace Thor\Database\PdoExtension;

use PDO;

/**
 * Class PdoHandler: holds a PDO configuration and instantiate a PDO object on demand.
 * @package Thor\Database\PdoExtension
 *
 * @since 2020-06
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
final class PdoHandler
{

    private string $dsn;

    private string $user;

    private string $password;

    private array $options;

    private ?PDO $pdo;

    /**
     * PdoHandler constructor.
     *
     * @param string $dsn
     * @param string $user
     * @param string $password
     * @param array $options (+ CASE=LOWER + FETCH_MODE=ASSOC + ERRMODE=EXCEPTION)
     */
    public function __construct(
        string $dsn,
        string $user = '',
        string $password = '',
        array $options = []
    ) {
        $this->pdo = null;
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        $this->options = $options + [
                PDO::ATTR_CASE => PDO::CASE_LOWER,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];
    }


    /**
     * getPdo(): instantiate it and returns.
     *
     * @return PDO
     */
    public function connect(): PDO
    {
        return new PDO(
            $this->dsn,
            $this->user,
            $this->password,
            $this->options
        );
    }

    /**
     * getPdo(): returns instantiated PDO or instantiate it and returns.
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo ?? $this->connect();
    }

}


