<?php

/**
 * @package          Trehinos/Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Http;

use Thor\Thor;
use Thor\Debug\Logger;
use Thor\Http\Server\WebServer;
use Thor\Factories\WebServerFactory;

class WebKernel extends HttpKernel
{

    public function __construct(protected WebServer $server)
    {
        parent::__construct($this->server);
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
        return new self(WebServerFactory::creatWebServerFromConfiguration($config)->produce());
    }

}
