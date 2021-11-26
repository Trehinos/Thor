<?php

/**
 * @package          Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Http\Routing;

use Thor\Http\Uri;
use RuntimeException;
use Thor\Http\UriInterface;
use Thor\Http\Request\RequestInterface;

final class Router
{

    private ?Route $errorRoute = null;
    private ?string $matched_routeName = null;

    public function __construct(private array $routes)
    {
    }

    /**
     * @param string $routeName
     * @param array  $params
     * @param array  $queryArguments
     *
     * @return UriInterface
     *
     */
    public function getUrl(string $routeName, array $params = [], array $queryArguments = []): UriInterface
    {
        $route = $this->getRoute($routeName);
        if (null === $route) {
            throw new RuntimeException("Route $routeName not found...");
        }

        $path = $route->getPath();
        foreach ($params as $paramName => $paramValue) {
            $path = str_replace("\$$paramName", "$paramValue", $path);
        }
        return Uri::fromGlobals()->withPath("index.php$path")->withQuery($queryArguments);
    }

    public function getRoute(string $name): ?Route
    {
        return $this->routes[$name] ?? null;
    }

    public function match(RequestInterface $request, string $prefix = ''): Route|false|null
    {
        $pathInfo = substr($request->getUri()->getPath(), strlen($prefix) + 1);
        $method = $request->getMethod();
        $pathInfo = str_starts_with($pathInfo, '/') ? $pathInfo : "/$pathInfo";

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
