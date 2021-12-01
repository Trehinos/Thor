<?php

namespace Thor\Factories;

use Thor\Http\Routing\Router;
use Thor\Http\Server\WebServer;
use Thor\Security\SecurityInterface;
use Thor\Database\PdoExtension\PdoCollection;

/**
 * A factory to create the WebServer.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class WebServerFactory
{

    public static function creatWebServerFromConfiguration(array $config): WebServer
    {
        $pdoCollection = PdoCollection::createFromConfiguration($config['database']);
        $server = self::produce(
            $router = RouterFactory::createRouterFromConfiguration($config['web-routes']),
            null,
            $pdoCollection,
            $config['language'],
            $config['twig']
        );

        $server->setSecurity(HttpServerFactory::produceSecurity($server, $config['security']));

        return $server;
    }

    public static function produce(
        Router $router,
        ?SecurityInterface $security,
        PdoCollection $pdoCollection,
        array $language,
        array $twig_config = []
    ): WebServer {
        $webServer = new WebServer($router, $security, $pdoCollection, $language);
        $twig = TwigFactory::createTwigFromConfiguration($webServer, $twig_config);
        $webServer->twig = $twig;
        return $webServer;
    }
}
