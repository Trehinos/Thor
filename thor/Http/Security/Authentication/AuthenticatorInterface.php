<?php

namespace Thor\Http\Security\Authentication;

use Thor\Http\Security\Identity\IdentityInterface;

/**
 * Authenticator interface of Thor security contexts.
 *
 * @package          Thor/Security/Authentication
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface AuthenticatorInterface
{

    /**
     * Authenticate an identity.
     *
     * After the execution of this method, `$this->current()` MUST return `$identity->getIdentifier()`
     * and `$this->isAuthenticated()` MUST return true.
     *
     * @param \Thor\Http\Security\Identity\IdentityInterface $identity
     *
     * @return void
     */
    public function authenticate(IdentityInterface $identity): void;

    /**
     * Returns the identifier of the current authenticated identity.
     *
     * @return string|null
     */
    public function current(): ?string;

    /**
     * De-authenticate the current authenticated identity.
     *
     * @return void
     */
    public function quash(): void;

    /**
     * Returns true if an identity is currently authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool;

}
