<?php

/**
 * @package Trehinos/Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Security\Identity;

/**
 * This interface provides a way to identify a user.
 */
interface IdentityInterface
{

    public function getIdentifier(): string;

    public function hasPassword(): bool;

    public function isPassword(string $clearPassword): bool;

}
