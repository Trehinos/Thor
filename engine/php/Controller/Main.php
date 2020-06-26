<?php

namespace Thor\Controller;

use Symfony\Component\Yaml\Yaml;
use Thor\Database\CrudHelper;
use Thor\Globals;
use Thor\Http\Response;
use Thor\Security\User;

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
                'menu' => Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'menu.yml'))
            ]
        );
    }

    public function hello(string $name): Response
    {
        return new Response("Hello $name");
    }

    public function createAdmin(): Response
    {
        $user = new User('admin', 'password');
        $userCrud = new CrudHelper(User::class, $this->getServer()->getRequester());
        $pid = $userCrud->createOne($user);

        return new Response("Admin $pid created.");
    }

    public function generateUrl(): Response
    {
        $routeName = Globals::get('routeName');
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
