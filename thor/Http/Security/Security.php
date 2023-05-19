<?php

namespace Thor\Http\Security;

use Thor\Http\Security\Identity\IdentityInterface;
use Thor\Http\Security\Identity\ProviderInterface;
use Thor\Http\Security\Authentication\AuthenticatorInterface;

/**
 * Default abstract implementation of a Thor's Security context.
 *
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
abstract class Security implements SecurityInterface
{

    /**
     * Security constructor.
     *
     * @param ProviderInterface                                         $provider
     * @param \Thor\Http\Security\Authentication\AuthenticatorInterface $authenticator
     * @param Firewall[]                                                $firewalls
     */
    public function __construct(
        private ProviderInterface $provider,
        private AuthenticatorInterface $authenticator,
        private array $firewalls = [],
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFirewalls(): array
    {
        return $this->firewalls;
    }

    /**
     * @param Firewall $firewall
     *
     * @return void
     */
    public function addFirewall(Firewall $firewall): void
    {
        $this->firewalls[] = $firewall;
    }

    /**
     * @inheritDoc
     */
    public function getAuthenticator(): AuthenticatorInterface
    {
        return $this->authenticator;
    }

    /**
     * @inheritDoc
     */
    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentIdentity(): ?IdentityInterface
    {
        return $this->getProvider()->getIdentity($this->getAuthenticator()->current());
    }

}
