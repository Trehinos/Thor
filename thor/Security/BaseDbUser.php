<?php

namespace Thor\Security;

use Thor\Database\PdoExtension\AdvancedPdoRow;
use Thor\Database\PdoExtension\PdoRowInterface;

abstract class BaseDbUser implements PdoRowInterface, UserInterface
{

    use AdvancedPdoRow;

    private static ?PasswordHasher $hashMaker = null;

    public function __construct(string $username = '', string $clearPassword = '')
    {
        $this->attributes['username'] = $username;
        self::$hashMaker ??= new PasswordHasher();
        $this->attributes['password'] = self::$hashMaker->hash($clearPassword);
    }

    public function getUsername(): string
    {
        return $this->attributes['username'];
    }

    public function setUsername(string $username): void
    {
        $this->attributes['username'] = $username;
    }

    public function hasPwdHashFor(string $clearPassword): bool
    {
        return PasswordHasher::verify($clearPassword, $this->attributes['password']);
    }

    public function setPwdHashFrom(string $clearPassword): void
    {
        $this->attributes['password'] = self::$hashMaker->hash($clearPassword);
    }

}
