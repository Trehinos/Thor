<?php

namespace Thor\Controller;

use Thor\Http\Response;
use Thor\Http\Server;

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

    public function view(string $fileName, array $params = [])
    {
        return new Response($this->server->getTwig()->render($fileName, $params));
    }

}
