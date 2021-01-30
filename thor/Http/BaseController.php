<?php

namespace Thor\Http;

use Thor\Debug\Logger;

abstract class BaseController
{

    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
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

    public function generateUrl(string $routeName, array $params = []): string
    {
        if (!$route = $this->server->getRouter()->getRoute($routeName)) {
            return '#generate-url-error';
        }

        return $this->server->getRouter()->getUrl($routeName, $params);
    }

    public function redirect(string $routeName, array $params = [])
    {
        return new Response('', 302, ['Location' => $this->generateUrl($routeName, $params)]);
    }

}
