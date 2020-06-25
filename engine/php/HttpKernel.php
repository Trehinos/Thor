<?php

namespace Thor;

use Thor\Database\PdoHandler;
use Thor\Database\PdoRequester;
use Thor\Http\Request;
use Thor\Http\Routing\Route;
use Thor\Http\Routing\Router;
use Thor\Http\Server;

use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

final class HttpKernel implements KernelInterface
{

    private Server $server;

    /**
     * HttpKernel constructor.
     *
     * @param array $configuration ['database' => ..., 'routes' => ..., 'twig' => ...]
     */
    public function __construct(array $configuration)
    {
        $router = self::createRouterFromConfiguration($configuration['routes'] ?? []);
        $twig = self::createTwigFromConfiguration($router, $configuration['twig'] ?? []);
        $requester = self::createRequesterFromConfiguration($configuration['database'] ?? []);

        $this->server = new Server(
            $twig,
            $requester,
            $router,
        );

        $this->addTwigFunctions();
    }

    private function addTwigFunctions()
    {
        $server = $this->server;
        $router = $server->getRouter();
        $twigEnv = $server->getTwig();

        $twigEnv->addFunction(
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
        $twigEnv->addFunction(
            new TwigFunction(
                'icon',
                function (string $icon, string $prefix = 'fas', bool $fixed = false) {
                    $fw = $fixed ? 'fa-fw' : '';
                    return "<i class='$prefix fa-$icon $fw'></i>";
                }
            )
        );
        $twigEnv->addFunction(
            new TwigFunction(
                'render',
                function (string $cClass, string $cMethod, array $params = []) use ($server) {
                    $cClass = "Thor\\Controller\\$cClass";
                    $controller = new $cClass($server);
                    return $controller->$cMethod(...$params)->getBody();
                }
            )
        );
    }

    private static function createRequesterFromConfiguration(array $db_config): PdoRequester
    {
        return new PdoRequester(
            new PdoHandler(
                $db_config['dsn'] ?? '',
                $db_config['user'] ?? '',
                $db_config['password'] ?? ''
            )
        );
    }

    private static function createRouterFromConfiguration(array $routes): Router
    {
        $routesObj = [];
        foreach ($routes as $routeName => $routeInfo) {
            $routesObj[$routeName] = new Route(
                $routeInfo['path'],
                $routeInfo['action']['class'],
                $routeInfo['action']['method'],
                $routeInfo['method'] ?? 'GET',
                $routeInfo['parameters'] ?? []
            );
        }
        return new Router($routesObj);
    }

    private static function createTwigFromConfiguration(Router $router, array $twig_config): Environment
    {
        $twigEnv = new Environment(
            new FilesystemLoader(Globals::CODE_DIR . ($twig_config['views_dir'] ?? '')),
            [
                'cache' => Globals::CODE_DIR . ($twig_config['cache_dir'] ?? ''),
                'debug' => Server::ENV !== Server::PROD
            ]
        );

        return $twigEnv;
    }

    public function execute()
    {
        ob_start();
        $response = $this->server->handle(Request::createFromServer());

        if (Server::ENV === Server::PROD) {
            ob_clean();                         // Prevent accidental echoes or var_dump from controller in prod
        }

        http_response_code($response->getStatus());                                 // Emit status code

        if (!empty($headers = $response->getHeaders())) {
            foreach ($headers as $headerName => $headerValue) {                     // Emit headers
                if (is_array($headerValue)) {
                    foreach ($headerValue as $subValue) {
                        header("$headerName: $subValue", false);
                    }
                    continue;
                }
                header("$headerName: $headerValue");
            }
        }

        if (($body = $response->getBody()) !== '') {
            echo $body;                                                             // Print body
        }
    }

}
