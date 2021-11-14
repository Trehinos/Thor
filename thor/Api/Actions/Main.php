<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Actions;

use Thor\Http\Request\HttpMethod;
use Symfony\Component\Yaml\Yaml;
use Thor\Globals;
use Thor\Http\Response\Response;
use Thor\Http\Routing\Route;
use Thor\Http\Controllers\WebController;

final class Main extends WebController
{

    #[Route('index', '/', HttpMethod::GET)]
    public function index(): Response
    {
        $menuItem = $this->get('menuItem');

        return $this->twigResponse(
            'page.html.twig',
            [
                'menuItem' => $menuItem
            ]
        );
    }

    #[Route('index-page', '/index', HttpMethod::GET)]
    public function indexPage(): Response
    {
        //$icons = Yaml::parseFile(Globals::STATIC_DIR . 'icons.yml');

        return $this->twigResponse(
            'pages/index.html.twig',
            [
                'routes' => $this->getServer()->getRouter()->getRoutes(),
//                'icons' => $icons
            ]
        );
    }

    #[Route('menu')]
    public function menu(): Response
    {
        return $this->twigResponse(
            'menu.html.twig',
            [
                'menu' => Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'menu.yml'))
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
