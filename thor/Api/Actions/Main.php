<?php

namespace Thor\Api\Actions;

use Symfony\Component\Yaml\Yaml;

use Thor\Http\BaseController;
use Thor\Api\Entities\User;
use Thor\Api\Managers\UserManager;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Debug\Logger;
use Thor\Globals;
use Thor\Http\Response;
use Thor\Http\Routing\Route;
use Thor\Http\Server;

final class Main extends BaseController
{

    #[Route('index', '/', 'GET')]
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

    #[Route('index-page', '/index', 'GET')]
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

    #[Route('changelog', '/changelog', 'GET')]
    public function changelog(): Response
    {
        return $this->view('pages/changelog.html.twig');
    }

    #[Route('about', '/about', 'GET')]
    public function about(): Response
    {
        return $this->view('pages/about.html.twig');
    }

    #[Route('legal', '/legal', 'GET')]
    public function legal(): Response
    {
        return $this->view('pages/legal.html.twig');
    }

    /**
     * POST /framework/create/admin
     *
     * @return Response
     */
    public function createAdmin(): Response
    {
        $userManager = new UserManager(new CrudHelper(User::class, $this->getServer()->getRequester()));
        $pid = $userManager->createUser('admin', 'password');

        Logger::write("Admin $pid created.", Logger::LEVEL_VERBOSE);
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
