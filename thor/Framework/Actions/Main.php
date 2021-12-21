<?php

namespace Thor\Framework\Actions;

use Thor\Globals;
use Symfony\Component\Yaml\Yaml;
use Thor\Http\{Routing\Route, Response\Response, Request\HttpMethod, Controllers\WebController};

/**
 * WebController, this is the main controller to serve Responses for the main routes of Thor.
 *
 * @internal
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
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

    #[Route('documentation', '/documentation', HttpMethod::GET)]
    public function documentationPage(): Response
    {
        return $this->twigResponse('pages/documentation.html.twig');
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
