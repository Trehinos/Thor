<?php

namespace Thor\Http;

use Thor\Database\PdoRequester;

use Thor\Http\Routing\Router;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Server
{

    const DEV = 'dev';
    const DEBUG = 'debug';
    const PROD = 'prod';

    const ENV = self::DEV;

    private Environment $twig;

    private PdoRequester $database;

    private Router $router;

    public function __construct(
        Environment $twig,
        PdoRequester $database,
        Router $router
    ) {
        $this->twig = $twig;
        $this->database = $database;
        $this->router = $router;
    }

    /**
     * Server->handle
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(Request $request): Response
    {
        $route = $this->router->match($request);

        if (null === $route) {
            return new Response404($this->twig->render('errors/404.html.twig'));
        }

        $params = $route->getFilledParams();
        $cClass = $route->getControllerClass();
        $cMethod = $route->getControllerMethod();

        $controller = new $cClass($this);
        return $controller->$cMethod(...array_values($params));
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }

    public function getRequester(): PdoRequester
    {
        return $this->database;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

}
