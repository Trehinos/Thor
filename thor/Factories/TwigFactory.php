<?php

/**
 * @package Trehinos/Thor/Factories
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Factories;

use Thor\Globals;
use Thor\Http\Server\WebServer;
use Symfony\Component\VarDumper\VarDumper;
use Thor\Http\Routing\Router;
use Thor\Thor;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigFactory
{

    private Environment $twig;

    public function __construct(private Router $router, ?Environment $twig = null)
    {
        $this->twig = $twig ?? new Environment(new FilesystemLoader());
    }


    public static function createTwigFromConfiguration(array $twig_config): Environment
    {
        return new Environment(
            new FilesystemLoader(
                array_map(
                    fn(string $folderName) => Globals::CODE_DIR . $folderName,
                    $twig_config['views_dir'] ?? ['']
                )
            ),
            [
                'cache' => Globals::VAR_DIR . ($twig_config['cache_dir'] ?? ''),
                'debug' => Thor::isDev(),
            ]
        );
    }

    public function produce(array $options = []): Environment
    {
        return $this->twig;
    }

    public function addDefaults(WebServer $server): self
    {
        $router = $this->router;

        $this->twig->addGlobal('server', $server);
        $this->twig->addGlobal('appName', Thor::appName());
        $this->twig->addGlobal('version', Thor::VERSION);
        $this->twig->addGlobal('versionName', Thor::VERSION_NAME);
        $this->twig->addGlobal('_', $server->getLanguage());

        $this->twig->addFunction(
            new TwigFunction(
                'url',
                function (string $routeName, array $params = [], string $queryString = '') use ($router): string {
                    return $router->getUrl($routeName, $params, $queryString);
                },
                ['is_safe' => ['html']]
            )
        );
        $this->twig->addFunction(
            new TwigFunction(
                'icon',
                function (string $icon, string $prefix = 'fas', bool $fixed = false, string $style = '') {
                    $fw = $fixed ? 'fa-fw' : '';
                    $style = ('' !== $style) ? "style='$style'" : '';
                    return "<i class='$prefix fa-$icon $fw' $style></i>";
                },
                ['is_safe' => ['html']]
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
                },
                ['is_safe' => ['html']]
            )
        );
        $this->twig->addFunction(
            new TwigFunction(
                'dump',
                function ($var) {
                    return VarDumper::dump($var);
                },
                ['is_safe' => ['html']]
            )
        );
        $this->twig->addFilter(
            new TwigFilter(
                'classname',
                fn($value) => substr($value, strrpos($value, '\\') + 1)
            )
        );
        $this->twig->addFilter(
            new TwigFilter(
                'DICT', // DICTIONARY
                function (string $str, array $arguments = []) use ($server) {
                    $foundStr = $server->getLanguage()[$str] ?? null;
                    if ($foundStr && !empty($arguments)) {
                        $foundStr = sprintf($foundStr, ...$arguments);
                    }
                    return $foundStr ?? $str;
                },
                ['is_safe' => ['html'], 'is_variadic' => true]
            )
        );

        return $this;
    }

}
