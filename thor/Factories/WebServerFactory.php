<?php

namespace Thor\Factories;

use Thor\Database\PdoExtension\PdoRequester;
use Thor\Http\Routing\Router;
use Thor\Http\Server\WebServer;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Security\SecurityInterface;

final class WebServerFactory
{

    public static function creatWebServerFromConfiguration(array $config): WebServer
    {
        $pdoCollection = PdoCollection::createFromConfiguration($config['database']);
        return self::produce(
            $router =
                (new RouterFactory(RouterFactory::createRoutesFromConfiguration($config['web-routes'])))
                    ->produce(),
            SecurityFactory::produceSecurity($router, new PdoRequester($pdoCollection->get()), $config['security']),
            $pdoCollection,
            $config['language'],
            $config['twig']
        );
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
