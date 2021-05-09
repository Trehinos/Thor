<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Actions;

use Thor\Debug\Logger;
use Thor\Http\Request;
use Thor\Http\Routing\Route;
use Thor\Http\BaseController;
use Thor\Http\Response;
use Thor\Http\Server;

final class Security extends BaseController
{

    public function __construct(Server $server)
    {
        parent::__construct($server);
    }

    #[Route('login', '/login', Request::GET)]
    public function login(): Response
    {
        return $this->view(
            'login.html.twig',
            []
        );
    }

    #[Route('check', '/security/login/action', Request::POST)]
    public function check(): Response
    {
        $username = Server::post('username');
        $password = Server::post('password');

        $token = $this->getServer()->getSecurity()?->authenticate($username, $password);

        if ($token) {
            Logger::write("User $username logged in.", Logger::LEVEL_DEBUG);
            return $this->redirect('index');
        }

        return $this->redirect($this->getServer()->getSecurity()?->loginRoute ?? 'login');
    }

    #[Route('logout', '/logout', Request::GET)]
    public function logout(): Response
    {
        $this->getServer()->getSecurity()?->deleteToken();
        return $this->redirect('index');
    }

}
