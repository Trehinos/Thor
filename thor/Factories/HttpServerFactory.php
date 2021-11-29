<?php

namespace Thor\Factories;

use Thor\Database\PdoExtension\PdoCollection;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Http\Routing\Router;
use Thor\Http\Server\HttpServer;
use Thor\Security\SecurityInterface;

final class HttpServerFactory
{

    public static function createHttpServerFromConfiguration(array $config): HttpServer
    {
        $pdoCollection = PdoCollection::createFromConfiguration($config['database']);
        return self::produce(
            $router = RouterFactory::createRouterFromConfiguration($config['api-routes']),
            SecurityFactory::produceSecurity($router, new PdoRequester($pdoCollection->get()), $config['security']),
            $pdoCollection,
            $config['language']
        );
    }

    public static function produce(
        Router $router,
        ?SecurityInterface $security,
        PdoCollection $pdoCollection,
        array $language
    ): HttpServer {
        return new HttpServer($router, $security, $pdoCollection, $language);
    }
}
