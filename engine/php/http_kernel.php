<?php

use Thor\Globals;
use Thor\Http\Request;
use Thor\Http\Routing\Route;
use Thor\Http\Routing\Router;
use Thor\Http\Server;
use Thor\Database\PdoHandler;
use Thor\Database\PdoRequester;

use Twig\Environment;
use Twig\Error\Error as TwigError;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Yaml\Yaml;
use Twig\TwigFunction;

// Load configuration files
$routes = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'routes.yml'));
$twig_config = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'twig.yml'));
$db_config = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'database.yml'));

// Construct routes for Router from configuration.
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
$router = new Router($routesObj);

// Construct Twig environment
$twigEnv = new Environment(
    new FilesystemLoader(Globals::CODE_DIR . ($twig_config['views_dir'] ?? '')),
    [
        'cache' => Globals::CODE_DIR . ($twig_config['cache_dir'] ?? ''),
        'debug' => Server::ENV !== Server::PROD
    ]
);
$twigEnv->addFunction(
    new TwigFunction(
        'url',
        function (string $routeName, array $params = []) use ($router) : string {
            $route = $router->getRoute($routeName);
            if (null === $route) {
                throw new TwigError("Twig, function 'url' : route '$routeName' not found.");
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
            return "index.php$path";
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

// Create server and handle the HTTP Request.
$server = new Server(
    $twigEnv,
    new PdoRequester(
        new PdoHandler(
            $db_config['dsn'],
            $db_config['user'],
            $db_config['password']
        )
    ),
    $router,
);


ob_start();
$response = $server->handle(Request::createFromServer());

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

