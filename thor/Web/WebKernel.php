<?php

namespace Thor\Web;

use Thor\Debug\Logger;
use Thor\Http\HttpKernel;
use Thor\Configuration\Configuration;
use Thor\Framework\Factories\{Configurations, WebServerFactory};
/**
 * WebKernel of Thor. It is by default instantiated with the `index.php` entry point.
 *
 * Works like the HttpKernel but with a WebServer instead of HttpServer.
 *
 * @see              WebServer
 *
 * @package          Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class WebKernel extends HttpKernel
{

    /**
     * @param WebServer $webServer
     */
    public function __construct(protected WebServer $webServer)
    {
        parent::__construct($webServer);
        Logger::write('Instantiate WebKernel');
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

        return self::createFromConfiguration(Configurations::getWebConfiguration());
    }

    /**
     * This static function returns a new WebKernel with specified configuration.
     *
     * @param Configuration $config
     *
     * @return static
     * @throws \ReflectionException
     * @throws \ReflectionException
     */
    public static function createFromConfiguration(Configuration $config): static
    {
        return new self(WebServerFactory::creatWebServerFromConfiguration($config));
    }

}
