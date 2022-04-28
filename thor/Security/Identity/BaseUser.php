<?php

namespace Thor\Security\Identity;

use Thor\Security\PasswordHasher;
use Thor\Security\Authorization\HasPermissions;

/**
 * This implementation of identity provide a simple User with username as identity and a hashed password.
 *
 * @package          Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class BaseUser implements IdentityInterface, HasPassword, HasPermissions, HasParameters
{

    protected string $hash;

    public function __construct(
        protected string $username,
        string $clearPassword,
        protected array $permissions = [],
        protected array $parameters = []
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

    public function setUsername(string $username): void
    {
        $this->username = $username;
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

    /**
     * @return bool always true
     */
    public function hasPassword(): bool
    {
        return true;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function addPermission(string $permission): void
    {
        $this->permissions[] = $permission;
    }

    public function removePermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            return;
        }
        $this->permissions[$permission] = null;
        unset($this->permissions[$permission]);
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param string[] $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function setParameter(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function getParameter(string $key): mixed
    {
        return $this->parameters[$key] ?? null;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

}
