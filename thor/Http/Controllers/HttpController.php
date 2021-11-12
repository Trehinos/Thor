<?php

/**
 * @package Trehinos/Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Http\Controllers;

use Thor\Http\Server\HttpServer;

abstract class HttpController
{

    public function __construct(protected HttpServer $server)
    {
    }

    public function getServer(): HttpServer
    {
        return $this->server;
    }
    
}
