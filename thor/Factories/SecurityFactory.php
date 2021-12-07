<?php

namespace Thor\Factories;

use Thor\Http\Routing\Router;
use Thor\Http\Server\HttpServer;
use Thor\Security\{Firewall, HttpSecurity, SecurityInterface};

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

    public static function produceSecurity(HttpServer $server, array $config): ?SecurityInterface
    {
        if (!($config['security'] ?? null)) {
            return null;
        }
        $firewalls = [];
        foreach ($config['firewall'] ?? [] as $firewallConfig) {
            $firewalls[] = self::produceFirewall($server->getRouter(), $firewallConfig);
        }

        return new HttpSecurity($server->getRequester($config['db-handler'] ?? 'default'), $firewalls);
    }

    public static function produceFirewall(Router $router, array $firewallConfig): Firewall
    {
        return new Firewall(
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
