<?php

namespace Thor\Security\Configuration;

use Thor\Security\Firewall;

interface SecurityConfigurationInterface // not needed, only class
{

    public function isEnabled(): bool;

    public function getIdentityProviderClass(): void;

    public function getIdentityType(): IdentityType; // todo delete

    public function getPdoRowClass(): ?string; // needed ?

    public function getUsernameField(): ?string;

    public function getFilename(): ?string;

    public function getFileField(): ?array;

    public function getFileSeparator(): ?string;

    public function getFilePhpStructure(): ?string;

    public function getLdapHost(): ?string;

    public function getLdapUser(): ?string;

    public function getLdapPassword(): ?string;

    public function getAuthenticatorClass(): void;

    public function getTokenKey(): string;

    public function getTokenExpire(): ?int;

}
