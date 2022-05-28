<?php

namespace Thor\Framework\Factories;

use Thor\Http\Routing\Router;
use Thor\Http\Server\HttpServer;
use Thor\Security\SecurityInterface;
use Thor\Configuration\Configuration;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Framework\Configurations\SecurityConfiguration;

/**
 * A factory to create HttpServers.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class HttpServerFactory
{

    private function __construct()
    {
    }

    public static function createHttpServerFromConfiguration(Configuration $config): HttpServer
    {
        $pdoCollection = PdoCollection::createFromConfiguration($config['database']);
        $server = self::produce(
            RouterFactory::createRouterFromConfiguration($config['routes']),
            null,
            $pdoCollection,
            $config['language']
        );

        $server->setSecurity(self::produceSecurity($server, $config['security']));

        return $server;
    }

    public static function produceSecurity(HttpServer $server, SecurityConfiguration $config): ?SecurityInterface
    {
        if (!$config->security()) {
            return null;
        }
        return $config->getSecurityFromFactory($server);
    }

    public static function produce(
        Router $router,
        ?SecurityInterface $security,
        PdoCollection $pdoCollection,
        Configuration $language
    ): HttpServer {
        return new HttpServer($router, $security, $pdoCollection, $language);
    }
}
