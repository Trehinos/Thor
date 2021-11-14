<?php

namespace Thor\Factories;

use Thor\Http\Server\WebServer;
use Thor\Database\PdoExtension\PdoCollection;

final class WebServerFactory extends Factory
{

    public function __construct(
        private RouterFactory $routerFactory,
        private SecurityFactory $securityFactory,
        private PdoCollection $pdoCollection,
        private array $language
    ) {
    }

    public static function creatWebServerFromConfiguration(array $config): WebServer
    {
        return (new self(
            new RouterFactory(RouterFactory::createRoutesFromConfiguration($config['routes'])),
            new SecurityFactory($config['security']),
            PdoCollection::createFromConfiguration($config['database']),
            $config['language'],

        ))->produce($config['twig']);
    }

    public function produce(array $options = []): WebServer
    {
        $router = $this->routerFactory->produce();
        $security = $this->securityFactory->produce();
        $webServer = new WebServer($router, $security, $this->pdoCollection, $this->language);
        $twig = TwigFactory::createTwigFromConfiguration($router, $webServer, $options);
        $webServer->twig = $twig;

        return $webServer;
    }
}
