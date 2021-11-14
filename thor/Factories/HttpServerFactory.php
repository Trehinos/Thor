<?php

namespace Thor\Factories;

use Thor\Http\Server\HttpServer;
use Thor\Database\PdoExtension\PdoCollection;

final class HttpServerFactory extends Factory
{

    public function __construct(
        private RouterFactory $routerFactory,
        private SecurityFactory $securityFactory,
        private PdoCollection $pdoCollection,
        private array $language
    ) {
    }

    public static function createHttpServerFromConfiguration(array $config): HttpServer
    {
        return (new self(
            new RouterFactory(RouterFactory::createRoutesFromConfiguration($config['api-routes'])),
            new SecurityFactory($config['security']),
            PdoCollection::createFromConfiguration($config['database']),
            $config['language']
        ))->produce();
    }

    public function produce(array $options = []): HttpServer
    {
        return new HttpServer(
            $this->routerFactory->produce(),
            $this->securityFactory->produce(),
            $this->pdoCollection,
            $this->language
        );
    }
}
