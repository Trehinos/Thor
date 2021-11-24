<?php

namespace Thor\Security\Authentication;

use Thor\Security\Identity\IdentityInterface;

interface AuthenticatorInterface
{

    public function authenticate(IdentityInterface $identity): void;

    public function quash(): void;

    public function isAuthenticated(): bool;

}
