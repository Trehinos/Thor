<?php

namespace Thor\Web\Assets;

use Thor\Debug\Logger;
use Thor\Web\WebKernel;
use Thor\Web\WebServer;
use Thor\Factories\Configurations;
use Thor\Configuration\Configuration;
use Thor\Factories\AssetServerFactory;
use Thor\Factories\TwigFunctionFactory;

class AssetsKernel extends WebKernel
{

    public function __construct(WebServer $server) {
        parent::__construct($server);
        $this->webServer->getTwig()->addFunction(TwigFunctionFactory::asset());
    }

    /**
     * This function return a new kernel.
     *
     * It loads the configuration files and use it to instantiate the Kernel.
     *
     * @see Configurations::getWebConfiguration()
     */
    public static function create(): static
    {
        self::guardHttp();
        Logger::write('Start Web context');

        return self::createFromConfiguration(Configurations::getAssetsConfiguration());
    }

    /**
     * This static function returns a new WebKernel with specified configuration.
     *
     * @param Configuration $config
     *
     * @return static
     */
    public static function createFromConfiguration(Configuration $config): static
    {
        return new self(AssetServerFactory::creatAssetServerFromConfiguration($config));
    }

}
