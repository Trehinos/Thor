<?php

namespace Thor\Factories;

use Thor\Database\PdoExtension\PdoRequester;
use Thor\Http\Routing\Router;
use Thor\Security\Firewall;
use Thor\Security\HttpSecurity;
use Thor\Security\SecurityInterface;

final class SecurityFactory
{

    public static function produceSecurity(Router $router, PdoRequester $requester, array $config): SecurityInterface
    {
        $firewalls = [];
        foreach ($config['firewall'] ?? [] as $firewallConfig) {
            $firewalls[] = self::produceFirewall($firewallConfig);
        }

        return new HttpSecurity($router, $requester, $firewalls);
    }

    public static function produceFirewall(array $firewallConfig): Firewall
    {
        return new Firewall(
            pattern: $firewallConfig['pattern'] ?? '/',
            redirect: $firewallConfig['redirect'] ?? 'login',
            loginRoute: $firewallConfig['login-route'] ?? 'login',
            logoutRoute: $firewallConfig['logout-route'] ?? 'logout',
            checkRoute: $firewallConfig['check-route'] ?? 'check',
            excludedRoutes: $firewallConfig['exclude-route'] ?? [],
            excludedPaths: $firewallConfig['exclude'] ?? [],
        );
    }

}
