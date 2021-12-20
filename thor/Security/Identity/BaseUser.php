<?php

namespace Thor\Security\Identity;

use Thor\Security\PasswordHasher;
use Thor\Security\Authorization\HasPermissions;

/**
 * This implementation of identity provide a simple User with username as identity and a hashed password.
 *
 * @package Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
class BaseUser implements IdentityInterface, HasPassword, HasPermissions
{

    protected string $hash;

    public function __construct(
        protected string $username,
        string $clearPassword,
        protected array $permissions = []
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
        return $this->hasPassword() === false || PasswordHasher::verify($clearPassword, $this->hash);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }
}
