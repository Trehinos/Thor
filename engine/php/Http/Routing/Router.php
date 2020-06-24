<?php

namespace Thor\Http\Routing;

use Thor\Http\Request;

final class Router
{

    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function getRoute(string $name) : ?Route
    {
        return $this->routes[$name] ?? null;
    }

    public function match(Request $request): ?Route
    {
        $pathInfo = $request->getPathInfo();
        $method = $request->getMethod();

        /**
         * @var Route $route
         */
        foreach ($this->routes as $route) {
            if ($route->matches($pathInfo, $method)) {
                return $route;
            }
        }

        return null;
    }

}
