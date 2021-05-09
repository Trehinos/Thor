<?php

/**
 * @package Trehinos/Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Security;

interface UserInterface
{

    public function getUsername(): string;

    public function hasPwdHashFor(string $clearPassword): bool;

}
