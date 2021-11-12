<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Actions;

use Thor\Debug\LogLevel;
use Thor\Http\Request\HttpMethod;
use Symfony\Component\Yaml\Yaml;

use Thor\Http\BaseController;
use Thor\Api\Entities\User;
use Thor\Api\Managers\UserManager;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Debug\Logger;
use Thor\Globals;
use Thor\Http\Response\Response;
use Thor\Http\Routing\Route;
use Thor\Http\Server;

final class Main extends BaseController
{

    #[Route('index', '/', HttpMethod::GET)]
    public function index(): Response
    {
        $menuItem = Server::get('menuItem');

        return $this->view(
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

        return $this->view(
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
        return $this->view(
            'menu.html.twig',
            [
                'menu' => Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'menu.yml'))
            ]
        );
    }

    #[Route('changelog', '/changelog', HttpMethod::GET)]
    public function changelog(): Response
    {
        return $this->view('pages/changelog.html.twig');
    }

    #[Route('about', '/about', HttpMethod::GET)]
    public function about(): Response
    {
        return $this->view('pages/about.html.twig');
    }

    #[Route('legal', '/legal', HttpMethod::GET)]
    public function legal(): Response
    {
        return $this->view('pages/legal.html.twig');
    }

    /**
     * POST /framework/create/admin
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function createAdmin(): Response
    {
        $userManager = new UserManager(new CrudHelper(User::class, $this->getServer()->getRequester()));
        $pid = $userManager->createUser('admin', 'password');

        Logger::write("Admin $pid created.", LogLevel::VERBOSE);
        return new Response();
    }

    /**
     * GET /framework/generate-url
     *
     * @return Response
     */
    public function generateUrlResponse(): Response
    {
        $routeName = Server::get('routeName');
        $route = $this->getServer()->getRouter()->getRoute($routeName);
        if (null === $route) {
            return new Response();
        }

        $param = [];
        foreach ($route->getParameters() as $pName => $pInfos) {
            $param[$pName] = $_GET["param-$pName"] ?? '';
        }

        return new Response($route->url($param));
    }

}
