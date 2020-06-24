<?php

use Thor\Globals;
use Thor\Http\Request;
use Thor\Http\Server;
use Thor\Database\PdoHandler;
use Thor\Database\PdoRequester;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Yaml\Yaml;

$routes = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'routes.yml'));
$twig_config = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'twig.yml'));
$db_config = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'database.yml'));

$server = new Server(
    new Environment(
        new FilesystemLoader(Globals::CODE_DIR . ($twig_config['views_dir'] ?? '')),
        [
            'cache' => Globals::CODE_DIR . ($twig_config['cache_dir'] ?? ''),
            'debug' => $twig_config['debug'] ?? true
        ]
    ),
    new PdoRequester(
        new PdoHandler(
            $db_config['dsn'],
            $db_config['user'],
            $db_config['password']
        )
    )
);
$response = $server->handle(Request::createFromServer());
echo $response->getBody();
