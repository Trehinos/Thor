<?php

namespace Thor\Factories;

use Thor\Http\Routing\Router;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Security\{Firewall, HttpSecurity, SecurityInterface};

/**
 * A factory the security context from configuration.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class SecurityFactory
{

    public static function produceSecurity(Router $router, PdoRequester $requester, array $config): ?SecurityInterface
    {
        if (!($config['security'] ?? null)) {
            return null;
        }
        $firewalls = [];
        foreach ($config['firewall'] ?? [] as $firewallConfig) {
            $firewalls[] = self::produceFirewall($router, $firewallConfig);
        }

        return new HttpSecurity($requester, $firewalls);
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
            excludedPaths:  $firewallConfig['exclude'] ?? [],
        );
    }

}
