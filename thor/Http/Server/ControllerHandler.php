<?php

namespace Thor\Http\Server;

use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\Http\Routing\Route;
use Thor\Http\Response\ResponseInterface;
use Thor\Http\Request\ServerRequestInterface;

/**
 * Handles a request by instantiating a controller and sending its response.
 *
 * @package Thor/Http/Server
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
class ControllerHandler implements RequestHandlerInterface
{

    public function __construct(protected HttpServer $httpServer, protected Route $route)
    {
    }

    /**
     * Instantiate the controller class from the Route given in the constructor and returns its response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $cClass = $this->route->getControllerClass();
        $cMethod = $this->route->getControllerMethod();
        Logger::write(' -> INSTANTIATE {controller} EXECUTE {method}', LogLevel::DEBUG, [
            'controller' => $cClass,
            'method'     => $cMethod,
        ]);
        $controller = new $cClass($this->httpServer);
        return $controller->$cMethod(...array_values($this->route->getFilledParams()));
    }

}
