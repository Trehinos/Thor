<?php

/**
 * @package Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoExtension;

use PDO;

final class PdoHandler
{

    private ?PDO $pdo = null;

    public function __construct(
        private string $dsn,
        private ?string $user = null,
        private ?string $password = null,
        private int $defaultCase = PDO::CASE_NATURAL
    ) {
    }

    public function getPdo(): PDO
    {
        return $this->pdo ??= new PDO(
            $this->dsn,
            $this->user,
            $this->password,
            [
                PDO::ATTR_CASE => $this->defaultCase,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

}
