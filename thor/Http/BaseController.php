<?php

namespace Thor\Http;

use Thor\Debug\Logger;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class BaseController
{

    public function __construct(private Server $server)
    {
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @param string $fileName
     * @param array $params
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function view(string $fileName, array $params = []): Response
    {
        Logger::write("     -> Twig : rendering file '$fileName'");
        return new Response($this->server->getTwig()->render($fileName, $params));
    }

    /**
     * @param string $routeName
     * @param array $params
     * @return string
     *
     * @throws Error
     */
    public function generateUrl(string $routeName, array $params = []): string
    {
        if (!$route = $this->server->getRouter()->getRoute($routeName)) {
            return '#generate-url-error';
        }

        return $this->server->getRouter()->getUrl($routeName, $params);
    }

    public function redirect(string $routeName, array $params = []): Response
    {
        return new Response(
            '', Response::STATUS_REDIRECT,
            ['Location' => $this->generateUrl($routeName, $params)]
        );
    }

}
