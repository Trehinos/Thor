<?php

namespace Thor\Http\Security\Identity;

/**
 *
 */

/**
 *
 */
interface HasPassword
{

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
