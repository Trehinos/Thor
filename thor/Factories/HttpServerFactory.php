<?php

namespace Thor\Factories;

use Thor\Database\PdoExtension\PdoCollection;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Http\Routing\Router;
use Thor\Http\Server\HttpServer;
use Thor\Security\SecurityInterface;

/**
 * A factory to create HttpServers.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class HttpServerFactory
{

    public static function createHttpServerFromConfiguration(array $config): HttpServer
    {
        $pdoCollection = PdoCollection::createFromConfiguration($config['database']);
        $server = self::produce(
            $router = RouterFactory::createRouterFromConfiguration($config['api-routes']),
            null,
            $pdoCollection,
            $config['language']
        );

        $server->setSecurity(self::produceSecurity($server, $config['security']));

        return $server;
    }

    public static function produceSecurity(HttpServer $server, array $config): ?SecurityInterface
    {
        if (!($config['security'] ?? false)) {
            return null;
        }
        $factoryString = $config['security-factory'];
        [$factoryClass, $factoryMethod] = explode(':', $factoryString);

        return $factoryClass::$factoryMethod($server, $config);
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
