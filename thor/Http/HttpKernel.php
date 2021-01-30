<?php

namespace Thor\Http;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Yaml\Yaml;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Database\PdoExtension\PdoHandler;

use Thor\Debug\Logger;
use Thor\Factories\TwigFactory;
use Thor\Globals;
use Thor\Http\Routing\Route;
use Thor\Http\Routing\Router;
use Thor\KernelInterface;

use Thor\Security\SecurityContext;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class HttpKernel implements KernelInterface
{

    private Server $server;

    public function __construct(
        #[ArrayShape([
            'databases' => 'array',
            'routes' => 'array',
            'twig' => 'array',
            'security' => 'array',
            'language' => 'array|null'
        ])]
        array $configuration
    ) {
        $router = self::createRouterFromConfiguration($configuration['routes'] ?? []);
        $twig = self::createTwigFromConfiguration($configuration['twig'] ?? []);
        $pdos = self::createDatabasesFromConfiguration($configuration['databases'] ?? []);

        Logger::write('Instantiate HttpKernel');
        $this->server = new Server(
            $twig,
            $pdos,
            $router,
            new SecurityContext($configuration['security'] ?? [], $pdos),
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
            new FilesystemLoader(
                array_map(
                    fn(string $folderName) => Globals::CODE_DIR . $folderName,
                    $twig_config['views_dir'] ?? ['']
                )
            ),
            [
                'cache' => Globals::CODE_DIR . ($twig_config['cache_dir'] ?? ''),
                'debug' => Server::ENV !== Server::PROD
            ]
        );
    }

    public function execute(): void
    {
        ob_start();
        Logger::write('Server handle the HTTP request');
        $request = Request::createFromServer();
        $response = $this->server->handle($request);
        Logger::write("HTTP Response generated (code : {$response->getStatus()}).", Logger::LEVEL_VERBOSE);

        if (Server::ENV === Server::PROD) {
            ob_clean(); // Prevent accidental echoes or var_dump from controller in prod
        }

        http_response_code($response->getStatus());                                 // Emit status code

        if (!empty($headers = $response->getHeaders())) {
            Logger::write("Send headers", Logger::LEVEL_DEV);
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
            Logger::write("Send body", Logger::LEVEL_DEV);
            echo $body;                                                             // Print body
        }
    }

    public static function create(array $config = []): self
    {
        if ('cli' === php_sapi_name()) {
            Logger::write(
                "PANIC ABORT : HTTP kernel tried to be executed from CLI context.",
                Logger::LEVEL_PROD,
                Logger::SEVERITY_ERROR
            );
            exit;
        }
        Logger::write('Start HTTP context');
        Logger::write('Load routes configuration');
        $routes = Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'routes.yml'));
        Logger::write('Load twig configuration');
        $twig = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'twig.yml'));
        Logger::write('Load security configuration');
        $security = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'security.yml'));
        $lang = $config['config']['lang'] ?? 'fr';
        Logger::write('Load language configuration');
        $language = Yaml::parse(file_get_contents(Globals::STATIC_DIR . "langs/$lang.yml"));
        return new self(
            [
                'routes' => $routes,
                'twig' => $twig,
                'databases' => $config['databases'] ?? [],
                'security' => $security,
                'language' => $language
            ]
        );
    }

}
