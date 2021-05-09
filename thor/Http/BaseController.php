<?php

/**
 * @package Trehinos/Thor/Http
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\Http;

use Thor\Debug\Logger;

abstract class BaseController
{

    public function __construct(private Server $server)
    {
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function view(string $fileName, array $params = []): Response
    {
        Logger::write("     -> Twig : rendering file '$fileName'");
        return new Response($this->server->getTwig()->render($fileName, $params));
    }

    public function generateUrl(string $routeName, array $params = [], string $queryString = ''): string
    {
        return $this->getServer()->generateUrl($routeName, $params, $queryString);
    }

    public function redirect(string $routeName, array $params = [], string $queryString = ''): Response
    {
        return $this->getServer()->redirect($routeName, $params, $queryString);
    }

    public function redirectTo(string $url): Response
    {
        return new Response('', 302, ['Location' => $url]);
    }


}
