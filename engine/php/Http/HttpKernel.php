<?php

namespace Thor\Http;

use Thor\Database\PdoHandler;
use Thor\Database\PdoRequester;
use Thor\Factories\TwigFactory;
use Thor\Globals;
use Thor\Http\Routing\Route;
use Thor\Http\Routing\Router;
use Thor\KernelInterface;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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
        $twig = self::createTwigFromConfiguration($configuration['twig'] ?? []);
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
        $twigFactory = new TwigFactory($server = $this->server, $server->getRouter(), $server->getTwig());
        $twigFactory->addDefaults();
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

    private static function createTwigFromConfiguration(array $twig_config): Environment
    {
        return new Environment(
            new FilesystemLoader(Globals::CODE_DIR . ($twig_config['views_dir'] ?? '')),
            [
                'cache' => Globals::CODE_DIR . ($twig_config['cache_dir'] ?? ''),
                'debug' => Server::ENV !== Server::PROD
            ]
        );
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
