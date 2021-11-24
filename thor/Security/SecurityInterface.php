<?php

namespace Thor\Security;

use Thor\Security\Configuration\SecurityConfigurationInterface;

interface SecurityInterface
{

    public function getConfiguration(): SecurityConfigurationInterface;

    /**
     * @return Firewall[]
     */
    public function getFirewalls(): array;

    public function getCurrentUser(): ?UserInterface;

    public function getCurrentToken(): ?string;

    public function protect(): void;

    public function authenticate(): void;

}
