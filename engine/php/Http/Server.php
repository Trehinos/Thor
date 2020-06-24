<?php

namespace Thor\Http;

use Thor\Database\PdoRequester;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Server
{

    private Environment $twig;

    private PdoRequester $database;

    public function __construct(
        Environment $twig,
        PdoRequester $database
    ) {
        $this->twig = $twig;
        $this->database = $database;
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
        // TODO : router
        return new Response404($this->twig->render('errors/404.html.twig'));
    }

}
