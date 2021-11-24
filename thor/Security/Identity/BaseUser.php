<?php

namespace Thor\Security\Identity;

use Thor\Security\PasswordHasher;

class BaseUser implements IdentityInterface
{

    protected string $hash;

    public function __construct(
        protected string $username,
        string $clearPassword
    ) {
        $this->hash = PasswordHasher::hashPassword($clearPassword);
    }

    public function getIdentifier(): string
    {
        return $this->username;
    }

    public function hasPassword(): bool
    {
        return true;
    }

    public function isPassword(string $clearPassword): bool
    {
        return PasswordHasher::verify($this->hash, $clearPassword);
    }

}
