<?php

namespace Thor\Database\PdoExtension;

use PDO;

final class PdoHandler
{

    private string $dsn;

    private string $user;

    private string $password;

    private ?PDO $pdo;

    public function __construct(
        string $dsn,
        string $user = '',
        string $password = ''
    )
    {
        $this->pdo = null;
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
    }

    public function getPdo(): PDO
    {
        return $this->pdo ?? $this->connect();
    }

    public function connect(): PDO
    {
        return $this->pdo ??= new PDO($this->dsn, $this->user, $this->password, [
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

}
