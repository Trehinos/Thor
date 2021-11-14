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
use Thor\Http\Server\WebServer;
use Thor\Http\Response\Response;

abstract class WebController extends HttpController
{

    public function __construct(protected WebServer $webServer)
    {
        parent::__construct($webServer);
    }

    public function getServer(): WebServer
    {
        return $this->webServer;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function twigResponse(string $fileName, array $params = []): Response
    {
        Logger::write("     -> Twig : rendering file '$fileName'");
        return Response::create($this->webServer->getTwig()->render($fileName, $params));
    }

}
