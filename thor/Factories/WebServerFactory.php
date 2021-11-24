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
                (new RouterFactory(RouterFactory::createRoutesFromConfiguration($config['api-routes'])))
                    ->produce(),
            SecurityFactory::produceSecurity($router, new PdoRequester($pdoCollection->get()), $config['security']),
            $pdoCollection,
            $config['language']
        );
    }


    public static function produce(
        Router $router,
        SecurityInterface $security,
        PdoCollection $pdoCollection,
        array $language,
        array $options = []
    ): WebServer {
        $webServer = new WebServer($router, $security, $pdoCollection, $language);
        $twig = TwigFactory::createTwigFromConfiguration($webServer, $options);
        $webServer->twig = $twig;
        return new WebServer($router, $security, $pdoCollection, $language, $twig);
    }
}
