<?php

namespace Thor\Factories;

use Thor\Http\Routing\Router;
use Thor\Http\Server\WebServer;
use Thor\Security\SecurityInterface;
use Thor\Configuration\Configuration;
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

    private function __construct()
    {
    }

    public static function creatWebServerFromConfiguration(Configuration $config): WebServer
    {
        $pdoCollection = PdoCollection::createFromConfiguration($config['database']);
        $server = self::produce(
            $router = RouterFactory::createRouterFromConfiguration($config['routes']),
            null,
            $pdoCollection,
            $config['language'],
            $config['twig']
        );

        $server->setSecurity(HttpServerFactory::produceSecurity($server, $config['security']));
        $server->getTwig()->addFunction(TwigFunctionFactory::authorized($server->getSecurity()));

        return $server;
    }

    public static function produce(
        Router $router,
        ?SecurityInterface $security,
        PdoCollection $pdoCollection,
        Configuration $language,
        ?Configuration $twig_config = null
    ): WebServer {
        $webServer = new WebServer($router, $security, $pdoCollection, $language);
        $twig = TwigFactory::createTwigFromConfiguration($webServer, $twig_config);
        $webServer->twig = $twig;
        return $webServer;
    }
}
