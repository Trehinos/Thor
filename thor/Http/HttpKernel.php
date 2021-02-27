<?php

namespace Thor\Http;

use Thor\Thor;
use Thor\Globals;
use ReflectionClass;
use ReflectionMethod;
use Twig\Environment;
use Thor\Debug\Logger;
use Thor\KernelInterface;
use Thor\Http\Routing\Route;
use Thor\Http\Routing\Router;
use Thor\Factories\TwigFactory;
use Twig\Loader\FilesystemLoader;
use JetBrains\PhpStorm\ArrayShape;
use Thor\Security\SecurityContext;
use Thor\Database\PdoExtension\PdoCollection;

final class HttpKernel implements KernelInterface
{

    private Server $server;

    public function __construct(
        #[ArrayShape([
            'config' => 'array',
            'database' => 'array',
            'routes' => 'array',
            'twig' => 'array',
            'security' => 'array',
            'language' => 'array|null',
        ])]
        array $configuration
    ) {
        $router = self::createRouterFromConfiguration($configuration['routes'] ?? []);
        $twig = self::createTwigFromConfiguration($configuration['twig'] ?? []);
        $pdos = PdoCollection::createFromConfiguration($configuration['database'] ?? []);

        Logger::write('Instantiate HttpKernel');
        $this->server = new Server(
            $configuration['config'],
            $twig,
            $pdos,
            $router,
            new SecurityContext($configuration['security'] ?? [], $pdos),
            $configuration['language'] ?? []
        );

        $twigFactory = new TwigFactory($this->server, $router, $twig);
        $twigFactory->addDefaults();
    }

    public static function createRouterFromConfiguration(array $routes): Router
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
                $routeInfo['method'] ?? 'GET',
                $routeInfo['parameters'] ?? [],
                $rClass,
                $rMethod
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
                'cache' => Globals::VAR_DIR . ($twig_config['cache_dir'] ?? ''),
                'debug' => Thor::isDev(),
            ]
        );
    }

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

    final public static function guardHttp(): void
    {
        if ('cli' === php_sapi_name()) {
            Logger::write(
                "PANIC ABORT : HTTP kernel tried to be executed from CLI context.",
                Logger::LEVEL_PROD,
                Logger::SEVERITY_ERROR
            );
            exit;
        }
    }

    public static function create(): static
    {
        self::guardHttp();
        Logger::write('Start HTTP context');

        return self::createFromConfiguration(Thor::getConfiguration()->getHttpConfiguration());
    }

    public static function createFromConfiguration(array $config = []): static
    {
        return new self($config);
    }

    public function execute(): void
    {
        ob_start();
        Logger::write('Server handle the HTTP request');
        $request = Request::createFromServer();
        $response = $this->server->handle($request);
        Logger::write("HTTP Response generated (code : {$response->getStatus()}).", Logger::LEVEL_VERBOSE);

        if (Thor::getEnv() === 'prod') {
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

}
