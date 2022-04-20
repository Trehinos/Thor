<?php

namespace Thor\Framework\Factories;

use Thor\Web\WebServer;
use Thor\Http\Routing\Router;
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
final class AssetServerFactory
{

    private function __construct()
    {
    }

    public static function creatAssetServerFromConfiguration(Configuration $config): WebServer
    {
        $pdoCollection = PdoCollection::createFromConfiguration($config['database']);
        $server = self::produce(
            $router = RouterFactory::createRouterFromRoutes(
                [
                    RouterFactory::createRoute('load-asset', '', '', '/$asset', 'GET', []),
                ]
            ),
            null,
            $pdoCollection,
            $config['language'],
            $config['twig']
        );

        $server->getRequest()->withAttribute('assetsManager', AssetsListFactory::produce($config['assets']));

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
