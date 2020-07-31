<?php

namespace Thor\App\Actions;

use Symfony\Component\Yaml\Yaml;

use Thor\App\Entities\User;
use Thor\App\Managers\UserManager;
use Thor\Database\CrudHelper;
use Thor\Debug\Logger;
use Thor\Globals;
use Thor\Http\BaseController;
use Thor\Http\Response;
use Thor\Http\Server;

final class Main extends BaseController
{

    /**
     * GET /
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->view(
            'pages/index.html.twig',
            [
                'routes' => $this->getServer()->getRouter()->getRoutes()
            ]
        );
    }

    /**
     * no matching path (called with a render, can be called by instance)
     *
     * @return Response
     */
    public function menu(): Response
    {
        return $this->view(
            'menu.html.twig',
            [
                'menu' => Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'menu.yml'))
            ]
        );
    }

    /**
     * GET /changelog
     *
     * @return Response
     */
    public function changelog(): Response
    {
        return $this->view('pages/changelog.html.twig');
    }

    /**
     * GET /about
     *
     * @return Response
     */
    public function about(): Response
    {
        return $this->view('pages/about.html.twig');
    }

    /**
     * GET /legal
     *
     * @return Response
     */
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

        Logger::write("Admin $pid created.", Logger::VERBOSE);
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
