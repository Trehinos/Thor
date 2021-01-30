<?php

namespace Thor\Controller;

use Thor\Debug\Logger;
use Thor\Http\Response;
use Thor\Http\Server;

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

    public function generateUrl(string $routeName, array $params = [], string $urlAppend = ''): string
    {
        return $this->getServer()->generateUrl($routeName, $params, $urlAppend);
    }

    public function redirect(string $routeName, array $params = [], string $urlAppend = ''): Response
    {
        return $this->getServer()->redirect($routeName, $params, $urlAppend);
    }

    public function redirectTo(string $url): Response
    {
        return new Response('', 302, ['Location' => $url]);
    }

}
