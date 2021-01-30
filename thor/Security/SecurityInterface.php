<?php

namespace Thor\Security;

interface SecurityInterface
{

    public function isSecurityActive(): bool;

    public function getUserClass(): string;

    public function getUsernameProperty(): string;

    public function getPasswordProperty(): string;

    public function getRolesHierarchy(): array;

    public static function hasRole(SecurityInterface $security, string $role, ?array $roleArray = null): bool;

    public function getBaseRole(): string;

    public function getFirewallPattern(): string;

    public function getLoginUrl(): string;

    public function getLogoutUrl(): string;

    public function getCheckUrl(): string;

}
