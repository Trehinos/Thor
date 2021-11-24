<?php

namespace Thor\Security;

use Thor\Http\Request\ServerRequestInterface;
use Thor\Http\Response\ResponseInterface;
use Thor\Security\Authentication\AuthenticatorInterface;
use Thor\Security\Identity\ProviderInterface;

interface SecurityInterface
{

    /**
     * @return Firewall[]
     */
    public function getFirewalls(): array;

    public function getAuthenticator(): AuthenticatorInterface;

    public function getProvider(): ProviderInterface;

    public function protect(ServerRequestInterface $request): ?ResponseInterface;

}
