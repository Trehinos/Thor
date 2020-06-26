<?php

namespace Thor\Factories;

use Thor\Http\Routing\Router;
use Thor\Http\Server;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigFactory
{

    private Server $server;
    private Router $router;
    private Environment $twig;

    public function __construct(Server $server, Router $router, ?Environment $twig = null)
    {
        $this->server = $server;
        $this->router = $router;
        $this->twig = $twig ?? new Environment(new FilesystemLoader());
    }

    public function produce(): Environment
    {
        return $this->twig;
    }

    public function addDefaults(): self
    {
        $server = $this->server;
        $router = $this->router;

        $this->twig->addFunction(
            new TwigFunction(
                'url',
                function (string $routeName, array $params = []) use ($router) : string {
                    $route = $router->getRoute($routeName);
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
            )
        );
        $this->twig->addFunction(
            new TwigFunction(
                'icon',
                function (string $icon, string $prefix = 'fas', bool $fixed = false) {
                    $fw = $fixed ? 'fa-fw' : '';
                    return "<i class='$prefix fa-$icon $fw'></i>";
                }
            )
        );
        $this->twig->addFunction(
            new TwigFunction(
                'render',
                function (string $cClass, string $cMethod, array $params = []) use ($server) {
                    $cClass = "Thor\\Controller\\$cClass";
                    $controller = new $cClass($server);
                    return $controller->$cMethod(...$params)->getBody();
                }
            )
        );
        $this->twig->addFilter(
            new TwigFilter(
                'classname',
                fn($value) => substr($value, strrpos($value, '\\') + 1)
            )
        );

        return $this;
    }

}
