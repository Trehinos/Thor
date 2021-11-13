<?php

namespace Thor\Http\Server;

use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\Http\Routing\Route;
use Thor\Http\Response\ResponseInterface;
use Thor\Http\Request\ServerRequestInterface;

class ControllerHandler implements RequestHandlerInterface
{

    public function __construct(private HttpServer $server, private Route $route)
    {
    }


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $cClass = $this->route->getControllerClass();
        $cMethod = $this->route->getControllerMethod();
        Logger::write(' -> INSTANTIATE {controller} EXECUTE {method}', LogLevel::DEBUG, [
            'controller' => $cClass,
            'method'     => $cMethod,
        ]);
        $controller = new $cClass($this->server);
        return $controller->$cMethod(...array_values($this->route->getFilledParams()));
    }
}
