<?php

namespace Thor\Framework\Factories;

use Thor\Http\Routing\Router;
use Thor\Http\Server\HttpServer;
use Thor\Configuration\Configuration;
use Thor\Framework\Security\HttpSecurity;
use Thor\Security\{Firewall, SecurityInterface};
use Thor\Framework\Configurations\SecurityConfiguration;

/**
 * A factory to create the security context and firewalls from configuration.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class SecurityFactory
{

    private function __construct()
    {
    }

    public static function produceSecurity(HttpServer $server, SecurityConfiguration $config): ?SecurityInterface
    {
        if (!$config->security()) {
            return null;
        }
        $security = new HttpSecurity($server->getRequester($config->pdoHandler()));
        foreach ($config->firewalls ?? [] as $firewallConfig) {
            $security->addFirewall(
                self::produceFirewall(
                    $security,
                    $server->getRouter(),
                    new Configuration($firewallConfig)
                )
            );
        }

        return $security;
    }

    public static function produceFirewall(
        SecurityInterface $security,
        Router $router,
        Configuration $firewallConfig
    ): Firewall {
        return new Firewall(
                            $security,
                            $router,
            pattern:        $firewallConfig['pattern'] ?? '/',
            redirect:       $firewallConfig['redirect'] ?? 'login',
            loginRoute:     $firewallConfig['login-route'] ?? 'login',
            logoutRoute:    $firewallConfig['logout-route'] ?? 'logout',
            checkRoute:     $firewallConfig['check-route'] ?? 'check',
            excludedRoutes: $firewallConfig['exclude-route'] ?? [],
            excludedPaths:  $firewallConfig['exclude-path'] ?? [],
        );
    }

}
