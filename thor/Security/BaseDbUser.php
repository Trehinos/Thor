<?php

namespace Thor\Security;

use Thor\Database\PdoExtension\PdoRowInterface;
use Thor\Database\PdoExtension\PdoRowTrait;

abstract class BaseDbUser implements PdoRowInterface, UserInterface
{

    use PdoRowTrait;

    private static ?PasswordHasher $hashMaker = null;

    private string $username;
    private string $pwd_hash;

    public function __construct(string $username = '', string $clearPassword = '')
    {
        self::$hashMaker ??= new PasswordHasher();
        $this->username = $username;
        $this->pwd_hash = self::$hashMaker->hash($clearPassword);
    }

    protected static function getTableColumns(): array
    {
        return [
            'username' => 'VARCHAR(255) NOT NULL',
            'password' => 'VARCHAR(255) NOT NULL',
        ];
    }

    protected function toPdo(): array
    {
        return [
            'username' => $this->getUsername(),
            'password' => $this->pwd_hash
        ];
    }

    protected function fromPdo(array $pdoArray)
    {
        $this->setUsername($pdoArray['username']);
        $this->pwd_hash = $pdoArray['password'];
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
        return PasswordHasher::verify($clearPassword, $this->pwd_hash);
    }

    public function setPwdHashFrom(string $clearPassword): void
    {
        $this->pwd_hash = self::$hashMaker->hash($clearPassword);
    }

}
