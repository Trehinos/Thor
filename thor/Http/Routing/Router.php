<?php

namespace Thor\Http\Routing;

use Thor\Http\Uri;
use RuntimeException;
use Thor\Tools\Strings;
use Thor\Http\UriInterface;
use Thor\Tools\PlaceholderFormat;
use Thor\Http\Request\RequestInterface;

/**
 * Describes a router.
 *
 * This class hold an array of routes which can match a Request.
 *
 * @package          Thor/Http/Routing
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
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

        $path = Strings::interpolate($route->getPath(), $params, PlaceholderFormat::SIGIL);
        return Uri::fromGlobals()->withPath("index.php$path")->withQuery($queryArguments);
    }

    /**
     * Gets a route from its name.
     *
     * @param string $name
     *
     * @return Route|null
     */
    public function getRoute(string $name): ?Route
    {
        return $this->routes[$name] ?? null;
    }

    /**
     * Returns the route corresponding the specified RequestInterface.
     *
     * Returns `false` if the HttpMethod is invalid.
     * The route will be retrievable from outside context with `$router->errorRoute`.
     *
     * Returns `null` if no Route was found for this request.
     * In this case, `$router->errorRoute` stays null.
     *
     * @param RequestInterface $request
     * @param string           $prefix
     *
     * @return Route|false|null
     */
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
                if (is_array($methods = $route->getMethod()) && in_array($method, $methods) || $method === $route->getMethod()) {
                    return $route;
                } else {
                    $this->errorRoute = $route;
                    return false;
                }
            }
        }

        return null;
    }

    /**
     * To be called after `$router->matches(...)`.
     *
     * Returns the matched route name or null if none matched.
     *
     * @return string|null
     */
    public function getMatchedRouteName(): ?string
    {
        return $this->matched_routeName;
    }

    /**
     * Returns the route which matches the request if the HttpMethod is invalid. Null otherwise.
     *
     * @return Route|null
     */
    public function getErrorRoute(): ?Route
    {
        return $this->errorRoute;
    }

    /**
     * Gets all the routes of this Router.
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

}
