<?php

namespace Thor\Security;

final class SecurityConfiguration implements SecurityInterface
{

    private bool $isActive = false;

    private string $userClass;
    private string $usernameProperty;
    private string $passwordProperty;
    private array $roles;
    private array $configuration;

    public function __construct(array $securityYml)
    {
        $this->userClass = $securityYml['userClass'] ?? '';
        $this->usernameProperty = $securityYml['usernameProperty'] ?? '';
        $this->passwordProperty = $securityYml['passwordProperty'] ?? '';
        $this->roles = $securityYml['roles'] ?? [];
        $this->configuration = $securityYml['configuration'];
    }

    /**
     * @return bool
     */
    public function isSecurityActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $active): void
    {
        $this->isActive = $active;
    }

    /**
     * @return string
     */
    public function getUserClass(): string
    {
        return $this->userClass;
    }

    /**
     * @return string
     */
    public function getUsernameProperty(): string
    {
        return $this->usernameProperty;
    }

    /**
     * @return string
     */
    public function getPasswordProperty(): string
    {
        return $this->passwordProperty;
    }

    /**
     * @return array
     */
    public function getRolesHierarchy(): array
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getBaseRole(): string
    {
        return $this->configuration['base-role'] ?? '';
    }

    /**
     * @return string
     */
    public function getFirewallPattern(): string
    {
        return $this->configuration['firewall-pattern'] ?? '';
    }

    /**
     * @return string
     */
    public function getLoginUrl(): string
    {
        return $this->configuration['login-url'] ?? '';
    }

    /**
     * @return string
     */
    public function getLogoutUrl(): string
    {
        return $this->configuration['logout-url'] ?? '';
    }

    /**
     * @return string
     */
    public function getCheckUrl(): string
    {
        return $this->configuration['check-url'] ?? '';
    }

    public static function hasRole(
        SecurityInterface $security,
        string $role,
        ?array $roleArray = null
    ): bool {
        foreach ($roleArray as $base_role) {
            if ($base_role === $role) {
                return true;
            } else {
                return self::hasRole($security, $base_role, $roleArray[$base_role]);
            }
        }

        return false;
    }
}
