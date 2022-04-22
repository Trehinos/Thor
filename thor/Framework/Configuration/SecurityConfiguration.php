<?php

namespace Thor\Framework\Configuration;

use Thor\Http\Server\HttpServer;
use Thor\Security\SecurityInterface;
use Thor\Configuration\ConfigurationFromFile;

final class SecurityConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('security');
    }

    public function security(): bool
    {
        return $this['security'] ?? false;
    }

    public function pdoHandler(): string
    {
        return $this['pdo-handler'] ?? 'default';
    }

    public function securityFactoryName(): ?string
    {
        return $this['security-factory'] ?? null;
    }

    public function getSecurityFromFactory(HttpServer $server): ?SecurityInterface
    {
        if ($this->securityFactoryName() === null) {
            return null;
        }
        [$classname, $method] = explode(':', $this->securityFactoryName());

        return $classname::$method($server, $this);
    }

}
