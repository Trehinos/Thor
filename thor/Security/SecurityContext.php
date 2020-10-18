<?php

namespace Thor\Security;

use DateTime;
use Thor\Http\Server;

final class SecurityContext
{

    private SecurityInterface $security;

    private ?string $username;

    private ?DateTime $expiration;

    public function __construct(
        SecurityInterface $security,
        ?string $username = null,
        ?DateTime $expiration = null
    ) {
        $this->security = $security;
        $this->security = $security;
        $this->security = $security;
         $this->username = $username;
        $this->expiration = $expiration;
    }

    public function isAuthenticated(): bool
    {
        return $this->security->isSecurityActive() && $this->username !== null;
    }

    public function hasExpired(): bool
    {
        if (null !== $this->expiration && $this->expiration > (new DateTime())) {
            return true;
        }

        return false;
    }

    public function setExpiration(?DateTime $expiration): void
    {
        $this->expiration = $expiration;
    }

    public function getExpiration(): ?DateTime
    {
        return $this->expiration;
    }

    public function getUsername(): ?string
    {
        if ($this->isAuthenticated()) {
            return $this->username;
        }

        return null;
    }

    public function isLoggedIn(): bool
    {
        return $this->isAuthenticated() && !$this->hasExpired();
    }

    public static function saveInSession(self $context, string $prefix = ''): void
    {
        Server::writeSession("{$prefix}SecurityUsername", $context->username);
        Server::writeSession("{$prefix}SecurityExpiration", $context->expiration);
    }

    public static function loadFromSession(SecurityInterface $security, string $prefix = ''): self
    {
        return new self(
            $security,
            Server::readSession("{$prefix}SecurityUsername"),
            Server::readSession("{$prefix}SecurityExpiration"),
        );
    }

}
