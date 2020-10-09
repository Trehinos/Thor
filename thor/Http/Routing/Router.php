<?php

namespace Thor\Http\Routing;

use Thor\Http\Request;
use Twig\Error\Error;

final class Router
{

    private array $routes;
    private ?Route $errorRoute = null;
    private ?string $matched_routeName = null;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param string $routeName
     * @param array $params
     *
     * @return string
     *
     * @throws Error
     */
    public function getUrl(string $routeName, array $params = []): string
    {
        $route = $this->getRoute($routeName);
        if (null === $route) {
            throw new Error("Twig, function 'url' : route '$routeName' not found.");
        }

        $path = $route->getPath();
        foreach ($params as $paramName => $paramValue) {
            $path = str_replace("\$$paramName", "$paramValue", $path);
        }
        if (substr($path, 0, 1) !== '/') {
            $path = "/$path";
            if ('/' === $path) {
                $path = '';
            }
        }
        return "/index.php$path";
    }

    public function getRoute(string $name): ?Route
    {
        return $this->routes[$name] ?? null;
    }

    /**
     * @param Request $request
     *
     * @return Route|false|null
     */
    public function match(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $method = $request->getMethod();

        /**
         * @var Route $route
         */
        foreach ($this->routes as $routeName => $route) {
            if ($route->matches($pathInfo)) {
                $this->matched_routeName = $routeName;
                if ($method === $route->getMethod()) {
                    return $route;
                } else {
                    $this->errorRoute = $route;
                    return false;
                }
            }
        }

        return null;
    }

    public function getMatchedRouteName(): ?string
    {
        return $this->matched_routeName;
    }

    public function getErrorRoute(): ?Route
    {
        return $this->errorRoute;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

}
