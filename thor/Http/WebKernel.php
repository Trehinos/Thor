<?php

namespace Thor\Http;

use Thor\Thor;
use Thor\Debug\Logger;
use Thor\Factories\Configurations;
use Thor\Factories\WebServerFactory;
use Thor\Configuration\Configuration;
use Thor\Http\{Server\WebServer, Response\ResponseInterface, Request\ServerRequestInterface};

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

    public function __construct(WebServer $webServer)
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
     */
    public static function createFromConfiguration(Configuration $config): static
    {
        return new self(WebServerFactory::creatWebServerFromConfiguration($config));
    }

    /**
     * Makes the WebServer handle the ServerRequestInterface and returns its ResponseInterface.
     */
    public function handle(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return $this->server->handle($serverRequest);
    }

}
