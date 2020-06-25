<?php

namespace Thor\Controller;

use Symfony\Component\Yaml\Yaml;
use Thor\Globals;
use Thor\Http\Response;

final class Main extends BaseController
{

    public function index(): Response
    {
        return $this->view('pages/index.html.twig');
    }

    public function menu(): Response
    {
        return $this->view(
            'menu.html.twig',
            [
                'menu' => Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'menu.yml'))
            ]
        );
    }

    public function hello(string $name): Response
    {
        return new Response("Hello $name");
    }

}
