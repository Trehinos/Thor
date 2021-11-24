<?php

namespace Thor\Security;

use Thor\Security\Authentication\AuthenticatorInterface;
use Thor\Security\Identity\ProviderInterface;

abstract class Security implements SecurityInterface
{

    /**
     * Security constructor.
     *
     * @param ProviderInterface      $provider
     * @param AuthenticatorInterface $authenticator
     * @param Firewall[]             $firewalls
     */
    public function __construct(
        private ProviderInterface $provider,
        private AuthenticatorInterface $authenticator,
        private array $firewalls = [],
    ) {
    }

    public function getFirewalls(): array
    {
        return $this->firewalls;
    }

    public function getAuthenticator(): AuthenticatorInterface
    {
        return $this->authenticator;
    }

    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }

}
