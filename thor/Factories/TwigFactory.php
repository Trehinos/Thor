<?php

namespace Thor\Factories;

use Symfony\Component\VarDumper\VarDumper;
use Thor\Http\Routing\Router;
use Thor\Http\Server;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigFactory
{

    private Environment $twig;

    public function __construct(private Server $server, private Router $router, ?Environment $twig = null)
    {
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

        $this->twig->addGlobal('server', $server);
        $this->twig->addGlobal('_', $server->getLanguage());

        $this->twig->addFunction(
            new TwigFunction(
                'url',
                function (string $routeName, array $params = []) use ($router) : string {
                    return $router->getUrl($routeName, $params);
                }
            )
        );
        $this->twig->addFunction(
            new TwigFunction(
                'icon',
                function (string $icon, string $prefix = 'fas', bool $fixed = false, string $style = '') {
                    $fw = $fixed ? 'fa-fw' : '';
                    $style = ('' !== $style) ? "style='$style'" : '';
                    return "<i class='$prefix fa-$icon $fw' $style></i>";
                }
            )
        );
        $this->twig->addFunction(
            new TwigFunction(
                'render',
                function (string $routeName, array $params = []) use ($server) {
                    $route = $server->getRouter()->getRoute($routeName);
                    $cClass = $route->getControllerClass();
                    $cMethod = $route->getControllerMethod();

                    $controller = new $cClass($server);
                    return $controller->$cMethod(...$params)->getBody();
                }
            )
        );
        $this->twig->addFunction(
            new TwigFunction(
                'dump',
                function ($var) {
                    return VarDumper::dump($var);
                }
            )
        );
        $this->twig->addFilter(
            new TwigFilter(
                'classname',
                fn($value) => substr($value, strrpos($value, '\\') + 1)
            )
        );
        $this->twig->addFilter( // TRANSLATE
            new TwigFilter(
                't',
                fn(string $str) => $server->getLanguage()[$str] ?? $str
            )
        );

        return $this;
    }

}
