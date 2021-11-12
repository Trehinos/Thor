<?php

namespace Thor\Factories;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Thor\Http\Routing\Route;
use JetBrains\PhpStorm\Pure;
use Thor\Http\Routing\Router;
use Thor\Http\Request\HttpMethod;

final class RouterFactory extends Factory
{

    public function __construct(private array $routesList)
    {
    }

    #[Pure]
    public function produce(): Router
    {
        return new Router($this->routesList);
    }

    /**
     * @param array $routes
     *
     * @return Route[]
     *
     * @throws ReflectionException
     */
    public static function createRoutesFromConfiguration(array $routes): array
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
                    foreach ($routeAttrs as $routeAttr) {
                        $route = $routeAttr->newInstance();
                        $route->setControllerClass($loadPath);
                        $route->setControllerMethod($method->getName());
                        $routesObj[$route->getRouteName()] = $route;
                    }
                }
            }
        }
        return $routesObj;
    }
}
