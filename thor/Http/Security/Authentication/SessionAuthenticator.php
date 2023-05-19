<?php

namespace Thor\Http\Security\Authentication;

use Thor\Http\Session;
use Thor\Http\Security\Identity\IdentityInterface;

/**
 * Authenticator interface of Thor security contexts.
 *
 * @package          Thor/Security/Authentication
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class SessionAuthenticator implements AuthenticatorInterface
{

    /**
     * @param string $key
     */
    public function __construct(private string $key = 'identity')
    {
    }

    /**
     * @inheritDoc
     */
    public function authenticate(IdentityInterface $identity): void
    {
        Session::write($this->key, $identity->getIdentifier());
    }

    /**
     * @inheritDoc
     */
    public function quash(): void
    {
        Session::write($this->key, null);
        Session::delete();
    }

    /**
     * @inheritDoc
     */
    public function isAuthenticated(): bool
    {
        return Session::read($this->key) !== null;
    }

    /**
     * @inheritDoc
     */
    public function current(): ?string
    {
        return Session::read($this->key);
    }
}
