<?php

namespace Thor\Database\PdoExtension;

use PDO;

/**
 * This class offer a PDO wrapper, to differ the database connection from the object instantiation.
 *
 * Use PdoHandler->getPdo() to get the PDO object and connect to the database.
 *
 * @see              PDO
 *
 * @package          Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class PdoHandler
{

    private ?PDO $pdo = null;

    /**
     * Constructs a new PdoHandler.
     */
    public function __construct(
        private string $dsn,
        private ?string $user = null,
        private ?string $password = null,
        private int $defaultCase = PDO::CASE_NATURAL,
        private array $customOptions = [],
    ) {
    }

    /**
     * Returns true if PDO object has been constructed, false otherwise.
     */
    public function isConnected(): bool
    {
        return $this->pdo !== null;
    }

    /**
     * Returns the current PDO object or constructs it with the PdoHandler parameters.
     */
    public function getPdo(): PDO
    {
        return $this->pdo ??= new PDO(
            $this->dsn,
            $this->user,
            $this->password,
            $this->customOptions +
            [
                PDO::ATTR_CASE               => $this->defaultCase,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

}
