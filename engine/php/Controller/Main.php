<?php

namespace Thor\Controller;

use Thor\Http\Response;

final class Main extends BaseController
{

    public function index(): Response
    {
        return $this->view('pages/index.html.twig');
    }

    public function hello(string $name): Response
    {
        return new Response("Hello $name");
    }

}
