<?php

namespace Thor\Factories;

use Thor\Http\Server\WebServer;
use Thor\Http\Server\HttpServer;
use Thor\Database\PdoExtension\PdoCollection;
use PhpOffice\PhpSpreadsheet\Calculation\Web;

final class WebServerFactory extends Factory
{

    public function __construct(
        private RouterFactory $routerFactory,
        private SecurityFactory $securityFactory,
        private PdoCollection $pdoCollection,
        private array $language,
        private array $twigConfiguration
    ) {
    }

    public static function creatWebServerFromConfiguration(array $config): self
    {
        return new self(
            new RouterFactory(RouterFactory::createRoutesFromConfiguration($config['routes'])),
            new SecurityFactory($config['security']),
            PdoCollection::createFromConfiguration($config['database']),
            $config['language'],
            $config['twig']
        );
    }

    public function produce(array $options = []): WebServer
    {
        $router = $this->routerFactory->produce();
        $security = $this->securityFactory->produce();
        $twigFactory = new TwigFactory(
            $router,
            $twig = TwigFactory::createTwigFromConfiguration($this->twigConfiguration)
        );
        $webServer = new WebServer($router, $security, $this->pdoCollection, $this->language, $twig);
        $twigFactory->addDefaults($webServer);

        return $webServer;
    }
}
