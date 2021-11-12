<?php

/**
 * @package Trehinos/Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Http\Controllers;

use Thor\Debug\Logger;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use JetBrains\PhpStorm\Pure;
use Thor\Http\Server\WebServer;
use Thor\Http\Response\Response;

abstract class WebController extends HttpController
{

    #[Pure]
    public function __construct(protected WebServer $server)
    {
        parent::__construct($server);
    }

    public function getServer(): WebServer
    {
        return $this->server;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function view(string $fileName, array $params = []): Response
    {
        Logger::write("     -> Twig : rendering file '$fileName'");
        return Response::create($this->server->getTwig()->render($fileName, $params));
    }

}
