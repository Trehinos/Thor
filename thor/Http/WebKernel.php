<?php

/**
 * @package          Trehinos/Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Http;

use Thor\Thor;
use Thor\Debug\Logger;
use Thor\Factories\WebServerFactory;
use Thor\Http\{Server\WebServer, Response\ResponseInterface, Request\ServerRequestInterface};

class WebKernel extends HttpKernel
{

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
        Logger::write('Instantiate WebKernel');
    }

    public static function create(): static
    {
        self::guardHttp();
        Logger::write('Start Web context');

        return self::createFromConfiguration(Thor::getConfiguration()->getWebConfiguration());
    }


    public static function createFromConfiguration(array $config = []): static
    {
        return new self(WebServerFactory::creatWebServerFromConfiguration($config));
    }

    public function handle(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return $this->server->handle($serverRequest);
    }

}
