<?php

namespace Thor\Framework\Factories;

use Thor\Http\Web\WebServer;
use Thor\Http\Routing\Router;
use JetBrains\PhpStorm\ArrayShape;
use Thor\Http\Security\SecurityInterface;
use Thor\Common\Configuration\Configuration;
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

    /**
     * @param \Thor\Common\Configuration\Configuration $config
     *
     * @return \Thor\Http\Web\WebServer
     * @throws \ReflectionException
     * @throws \ReflectionException
     */
    public static function creatWebServerFromConfiguration(
        #[ArrayShape(['database' => '', 'routes' => '', 'language' => '', 'twig' => ''])]
        Configuration $config
    ): WebServer
    {
        $pdoCollection = PdoCollection::createFromConfiguration($config['database']);
        $server = self::produce(
            RouterFactory::createRouterFromConfiguration($config['routes']),
            null,
            $pdoCollection,
            $config['language'],
            $config['twig']
        );

        $server->setSecurity(HttpServerFactory::produceSecurity($server, $config['security']));
        $server->getTwig()->addFunction(TwigFunctionFactory::authorized($server->getSecurity()));

        return $server;
    }

    /**
     * @param Router                                        $router
     * @param SecurityInterface|null                        $security
     * @param PdoCollection                                 $pdoCollection
     * @param \Thor\Common\Configuration\Configuration      $language
     * @param \Thor\Common\Configuration\Configuration|null $twig_config
     *
     * @return \Thor\Http\Web\WebServer
     * @throws \Exception
     * @throws \Exception
     */
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
