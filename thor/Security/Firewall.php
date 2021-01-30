<?php

namespace Thor\Security;

use DateTime;
use Thor\Http\Request;
use Thor\Http\Routing\Router;

final class Firewall
{

    public SecurityInterface $security;

    public Router $router;

    public function __construct(
        SecurityInterface $security,
        Router $router
    ) {
        $this->security = $security;
        $this->router = $router;
    }

    public function matches(string $pathInfo): bool
    {
        $pattern = $this->security->getFirewallPattern();
        if (preg_match("/$pattern/", $pathInfo)) {
            return true;
        }

        return false;
    }

    public function isAuthorized(UserInterface $user, string $currentRoutename): bool
    {
        $routeRoles = $this->router->getRoute($currentRoutename);

        foreach ($routeRoles as $role) {
            if (!SecurityConfiguration::hasRole($this->security, $role)) {
                return false;
            }
        }

        return true;
    }

}

