<?php

namespace Thor\Database\PdoExtension;

use PDO;

final class PdoHandler
{

    private ?PDO $pdo = null;

    public function __construct(
        private string $dsn,
        private string $user = '',
        private string $password = ''
    )
    {
    }

    public function getPdo(): PDO
    {
        return $this->pdo ?? $this->connect();
    }

    public function connect(): PDO
    {
        return $this->pdo ??= new PDO($this->dsn, $this->user, $this->password, [
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

}
