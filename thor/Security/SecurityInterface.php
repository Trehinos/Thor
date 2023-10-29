<?php

namespace Thor\Security;

use Thor\Http\Request\ServerRequestInterface;
use Thor\Http\Response\ResponseInterface;
use Thor\Http\Server\MiddlewareInterface;
use Thor\Http\Server\RequestHandlerInterface;
use Thor\Security\Identity\IdentityInterface;
use Thor\Security\Authentication\AuthenticatorInterface;
use Thor\Security\Identity\ProviderInterface;

/**
 * Interface of a Security context of Thor.
 *
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface SecurityInterface extends MiddlewareInterface
{

    /**
     * Gets the firewalls of the security context.
     *
     * @return Firewall[]
     */
    public function getFirewalls(): array;

    /**
     * Gets the authenticator of this context.
     *
     * @return AuthenticatorInterface
     */
    public function getAuthenticator(): AuthenticatorInterface;

    /**
     * Gets the provider of this context.
     *
     * @return ProviderInterface
     */
    public function getProvider(): ProviderInterface;

    /**
     * Protect the server from a Request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;

    /**
     * Gets the current authenticated identity.
     *
     * @return IdentityInterface|null
     */
    public function getCurrentIdentity(): ?IdentityInterface;

}
