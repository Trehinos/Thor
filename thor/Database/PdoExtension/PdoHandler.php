<?php

namespace Thor\Database\PdoExtension;

use PDO;
use PDOException;

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
        public readonly string $dsn,
        public readonly ?string $user = null,
        public readonly ?string $password = null,
        public readonly int $defaultCase = PDO::CASE_NATURAL,
        public readonly array $customOptions = [],
    ) {
    }

    public function getDriverName(): ?string
    {
        return explode(':', $this->dsn)[0] ?: null;
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
     *
     * @throws PDOException
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
