<?php

namespace Thor\Http;

use Thor\Database\PdoExtension\PdoCollection;
use Thor\Database\PdoExtension\PdoHandler;

use Thor\Debug\Logger;
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
        $pdos = self::createDatabasesFromConfiguration($configuration['databases'] ?? []);

        Logger::write('Instantiate HttpKernel');
        $this->server = new Server(
            $twig,
            $pdos,
            $router,
            $configuration['language'] ?? []
        );

        $twigFactory = new TwigFactory($this->server, $router, $twig);
        $twigFactory->addDefaults();
    }

    private static function createDatabasesFromConfiguration(array $db_config): PdoCollection
    {
        $pdos = new PdoCollection();

        foreach ($db_config as $connectionName => $config) {
            $pdos->add(
                $connectionName,
                new PdoHandler(
                    $config['dsn'] ?? '',
                    $config['user'] ?? '',
                    $config['password'] ?? ''
                )
            );
        }

        return $pdos;
    }

    private static function createRouterFromConfiguration(array $routes): Router
    {
        $routesObj = [];
        foreach ($routes as $routeName => $routeInfo) {
            $routesObj[$routeName] = new Route(
                $routeInfo['action']['class'],
                $routeInfo['action']['method'],
                $routeInfo['path'] ?? '',
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
        Logger::write('Server handle the HTTP request');
        $request = Request::createFromServer();
        $response = $this->server->handle($request);
        Logger::write("HTTP Response generated (code : {$response->getStatus()}).", Logger::VERBOSE);

        if (Server::ENV === Server::PROD) {
            ob_clean();                         // Prevent accidental echoes or var_dump from controller in prod
        }

        http_response_code($response->getStatus());                                 // Emit status code

        if (!empty($headers = $response->getHeaders())) {
            Logger::write("Send headers", Logger::DEV);
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

        if ($request->responseHasBody && ($body = $response->getBody()) !== '') {
            Logger::write("Send body", Logger::DEV);
            echo $body;                                                             // Print body
        }
    }

}
