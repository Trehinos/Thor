<?php

namespace Thor\App\Actions;

use Symfony\Component\Yaml\Yaml;

use Thor\App\Entities\User;
use Thor\Controller\BaseController;
use Thor\Database\CrudHelper;
use Thor\Debug\Logger;
use Thor\Globals;
use Thor\Http\Response;
use Thor\Http\Server;

final class Main extends BaseController
{

    public function index(): Response
    {
        return $this->view(
            'pages/index.html.twig',
            [
                'routes' => $this->getServer()->getRouter()->getRoutes()
            ]
        );
    }

    public function menu(): Response
    {
        return $this->view(
            'menu.html.twig',
            [
                'menu' => Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'menu.yml'))
            ]
        );
    }

    public function changelog(): Response
    {
        return $this->view('pages/changelog.html.twig');
    }

    public function about(): Response
    {
        return $this->view('pages/about.html.twig');
    }

    public function legal(): Response
    {
        return $this->view('pages/legal.html.twig');
    }

    public function createAdmin(): Response
    {
        $user = new User('admin', 'password');
        $userCrud = new CrudHelper(User::class, $this->getServer()->getRequester());
        $pid = $userCrud->createOne($user);

        Logger::write("Admin $pid created.", Logger::VERBOSE);
        return new Response();
    }

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
