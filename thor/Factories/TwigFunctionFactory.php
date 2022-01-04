<?php

namespace Thor\Factories;

use Twig\TwigFunction;
use Thor\Http\Routing\Router;
use Thor\Http\Server\WebServer;
use Thor\Security\SecurityInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * A factory to create twig Functions.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class TwigFunctionFactory
{

    private function __construct()
    {
    }

    public static function authorized(SecurityInterface $security): TwigFunction
    {
        return new TwigFunction(
            'authorized',
            function (string ...$permissions) use ($security): bool {
                $identity = $security->getCurrentIdentity();
                foreach ($permissions as $permission) {
                    if (!$identity->hasPermission($permission)) {
                        return false;
                    }
                }
                return true;
            },
        );
    }

    public static function url(Router $router): TwigFunction
    {
        return new TwigFunction(
            'url',
            function (string $routeName, array $params = [], array $query = []) use ($router): string {
                return "{$router->getUrl($routeName, $params, $query)}";
            },
            ['is_safe' => ['html']]
        );
    }

    public static function icon(): TwigFunction
    {
        return new TwigFunction(
            'icon',
            function (string $icon, string $prefix = 'fas', bool $fixed = false, string $style = '') {
                $fw = $fixed ? 'fa-fw' : '';
                $style = ('' !== $style) ? "style='$style'" : '';
                return "<i class='$prefix fa-$icon $fw' $style></i>";
            },
            ['is_safe' => ['html']]
        );
    }

    public static function render(WebServer $server): TwigFunction
    {
        return new TwigFunction(
            'render',
            function (string $routeName, array $params = []) use ($server) {
                $route = $server->getRouter()->getRoute($routeName);
                $cClass = $route->getControllerClass();
                $cMethod = $route->getControllerMethod();

                $controller = new $cClass($server);
                return $controller->$cMethod(...$params)->getBody();
            },
            ['is_safe' => ['html']]
        );
    }

    public static function dump(): TwigFunction
    {
        return new TwigFunction(
            'dump',
            function ($var) {
                return VarDumper::dump($var);
            },
            ['is_safe' => ['html']]
        );
    }

    public static function uuid(int $defaultSize = 8): TwigFunction
    {
        return new TwigFunction(
            'uuid',
            fn(?int $size = null) => bin2hex(random_bytes($size ?? $defaultSize))
        );
    }

}
