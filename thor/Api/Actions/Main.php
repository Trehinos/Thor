<?php

/**
 * Main WebController to serve the main default routes of Thor.
 *
 * @package          Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Api\Actions;

use Symfony\Component\Yaml\Yaml;
use Thor\Globals;
use Thor\Http\{Controllers\WebController, Request\HttpMethod, Response\Response, Routing\Route};

final class Main extends WebController
{

    #[Route('index', '/', HttpMethod::GET)]
    public function index(): Response
    {
        return $this->twigResponse('page.html.twig', ['menuItem' => $this->get('menuItem')]);
    }

    #[Route('index-page', '/index', HttpMethod::GET)]
    public function indexPage(): Response
    {
        return $this->twigResponse(
            'pages/index.html.twig',
            [
                'routes' => $this->getServer()->getRouter()->getRoutes(),
            ]
        );
    }

    #[Route('menu')]
    public function menu(): Response
    {
        return $this->twigResponse(
            'menu.html.twig',
            [
                'menu' => Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'menu.yml')),
            ]
        );
    }

    #[Route('changelog', '/changelog', HttpMethod::GET)]
    public function changelog(): Response
    {
        return $this->twigResponse('pages/changelog.html.twig');
    }

    #[Route('about', '/about', HttpMethod::GET)]
    public function about(): Response
    {
        return $this->twigResponse('pages/about.html.twig');
    }

    #[Route('legal', '/legal', HttpMethod::GET)]
    public function legal(): Response
    {
        return $this->twigResponse('pages/legal.html.twig');
    }

}
