<?php

namespace Thor\Security\Identity;

use Thor\Security\PasswordHasher;

/**
 * This implementation of identity provide a simple User with username as identity and a hashed password.
 *
 * @package Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
class BaseUser implements IdentityInterface
{

    protected string $hash;

    public function __construct(
        protected string $username,
        string $clearPassword
    ) {
        $this->hash = PasswordHasher::hashPassword($clearPassword);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @return bool always true
     */
    public function hasPassword(): bool
    {
        return true;
    }

    /**
     * Returns true if the specified clear password correspond this user's password.
     *
     * @param string $clearPassword
     *
     * @return bool
     */
    public function isPassword(string $clearPassword): bool
    {
        return PasswordHasher::verify($clearPassword, $this->hash);
    }

}
