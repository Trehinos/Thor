<?php

namespace Thor\Security\Identity;

/**
 * This interface provides a way to identify a user.
 *
 * @package Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface IdentityInterface
{

    /**
     * Gets this identity identifier (a unique string identifying this identity).
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Return true if this identity has a password.
     *
     * @return bool
     */
    public function hasPassword(): bool;

    /**
     * Returns true if the specified clear password correspond this identity's password.
     *
     * If hasPassword() === false, this method MUST return true.
     *
     * @param string $clearPassword
     *
     * @return bool
     */
    public function isPassword(string $clearPassword): bool;

}
