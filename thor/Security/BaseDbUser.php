<?php

namespace Thor\Security;

use Thor\Database\PdoExtension\AdvancedPdoRow;
use Thor\Database\PdoExtension\PdoRowInterface;
use Thor\Database\PdoExtension\Attributes\PdoColumn;
use Thor\Database\PdoExtension\Attributes\PdoIndex;

#[PdoIndex(['username'], null, true)]
#[PdoColumn('username', 'VARCHAR(255)', 'string')]
#[PdoColumn('password', 'VARCHAR(255)', 'string')]
abstract class BaseDbUser implements PdoRowInterface, UserInterface
{

    use AdvancedPdoRow {
        AdvancedPdoRow::__construct as private adwConstruct;
    }

    private static ?PasswordHasher $hashMaker = null;

    private string $password;

    public function __construct(
        private string $username = '',
        string $clearPassword = '',
        string $public_id = null,
        array $primary = [null]
    ) {
        $this->adwConstruct($public_id, $primary);
        self::$hashMaker ??= new PasswordHasher();
        $this->password = self::$hashMaker->hash($clearPassword);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function hasPwdHashFor(string $clearPassword): bool
    {
        return PasswordHasher::verify($clearPassword, $this->password);
    }

    public function setPwdHashFrom(string $clearPassword): void
    {
        $this->password = self::$hashMaker->hash($clearPassword);
    }

}
