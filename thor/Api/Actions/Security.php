<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Actions;

use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\Http\Request\HttpMethod;
use Thor\Http\Routing\Route;
use Thor\Http\BaseController;
use Thor\Http\Response\Response;
use Thor\Http\Server;

final class Security extends BaseController
{

    public function __construct(Server $server)
    {
        parent::__construct($server);
    }

    #[Route('login', '/login', HttpMethod::GET)]
    public function login(): Response
    {
        return $this->view(
            'login.html.twig',
            []
        );
    }

    #[Route('check', '/security/login/action', HttpMethod::POST)]
    public function check(): Response
    {
        $username = Server::post('username');
        $password = Server::post('password');

        $token = $this->getServer()->security?->authenticate($username, $password);

        if ($token) {
            Logger::write("User $username logged in.", LogLevel::DEBUG);
            return $this->redirect('index');
        }

        return $this->redirect($this->getServer()->security?->loginRoute ?? 'login');
    }

    #[Route('logout', '/logout', HttpMethod::GET)]
    public function logout(): Response
    {
        $this->getServer()->security?->deleteToken();
        return $this->redirect('index');
    }

}
