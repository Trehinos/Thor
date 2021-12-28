<?php

namespace Thor\Factories;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Thor\Http\Routing\Route;
use Thor\Http\Routing\Router;
use Thor\Http\Request\HttpMethod;
use Thor\Configuration\Configuration;
use Thor\Configuration\RoutesConfiguration;
use Thor\Security\Authorization\Authorization;

/**
 * The router from configuration.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class RouterFactory
{

    private function __construct()
    {
    }

    public static function createRouterFromConfiguration(RoutesConfiguration $routes): Router
    {
        return new Router(self::createRoutesFromConfiguration($routes));
    }

    /**
     * @param RoutesConfiguration $routes
     *
     * @return Route[]
     *
     * @throws ReflectionException
     */
    public static function createRoutesFromConfiguration(RoutesConfiguration $routes): array
    {
        $routesObj = [];
        foreach ($routes as $routeName => $routeInfo) {
            if ($routeName === 'load') {
                $routesObj = self::loadRouteAttr($routesObj, $routeInfo);
                continue;
            }
            $rClass = $routeInfo['action']['class'];
            $rMethod = $routeInfo['action']['method'];

            $routesObj[$routeName] = new Route(
                $routeName,
                $routeInfo['path'] ?? '',
                HttpMethod::tryFrom($routeInfo['method'] ?? 'GET'),
                $routeInfo['parameters'] ?? [],
                $rClass,
                $rMethod
            );
        }
        return $routesObj;
    }

    /**
     * @param array $routesObj
     * @param array $pathsList
     *
     * @return array
     *
     * @throws ReflectionException
     */
    private static function loadRouteAttr(array $routesObj, array $pathsList): array
    {
        foreach ($pathsList as $loadPath) {
            $rc = new ReflectionClass($loadPath);
            foreach ($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (!empty($routeAttrs = $method->getAttributes(Route::class))) {
                    $authorization = ($method->getAttributes(Authorization::class)[0] ?? null)?->newInstance();
                    foreach ($routeAttrs as $routeAttr) {
                        /** @var Route $route */
                        $route = $routeAttr->newInstance();
                        $route->setControllerClass($loadPath);
                        $route->setControllerMethod($method->getName());
                        $route->authorization = $authorization;
                        $routesObj[$route->getRouteName()] = $route;
                    }
                }
            }
        }
        return $routesObj;
    }
}
