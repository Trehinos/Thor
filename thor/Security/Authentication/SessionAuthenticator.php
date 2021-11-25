<?php

namespace Thor\Security\Authentication;

use Thor\Http\Session;
use Thor\Security\Identity\BaseUser;
use Thor\Security\Identity\IdentityInterface;

class SessionAuthenticator implements AuthenticatorInterface
{

    public function __construct(private string $key = 'identity')
    {
    }

    public function authenticate(IdentityInterface $identity): void
    {
        Session::write($this->key, $identity->getIdentifier());
    }

    public function quash(): void
    {
        Session::write($this->key, null);
    }

    public function isAuthenticated(): bool
    {
        return Session::read($this->key) !== null;
    }

    public function current(): ?string
    {
        return Session::read($this->key);
    }
}
