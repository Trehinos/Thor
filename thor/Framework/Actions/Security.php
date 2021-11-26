<?php

/**
 * WebController of security actions (login, check, logout).
 *
 * @package          Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Framework\Actions;

use Thor\Debug\{Logger, LogLevel};
use Thor\Http\{Routing\Route, Request\HttpMethod, Controllers\WebController, Response\ResponseInterface};

final class Security extends WebController
{

    #[Route('login', '/login', HttpMethod::GET)]
    public function login(): ResponseInterface
    {
        return $this->twigResponse('login.html.twig');
    }

    #[Route('check', '/check', HttpMethod::POST)]
    public function check(): ResponseInterface
    {
        $username = $this->post('username');
        $password = $this->post('password');
        $user = $this->getServer()?->getSecurity()->getProvider()->getIdentity($username);
        if ($user && $user->isPassword($password)) {
            $this->getServer()->getSecurity()->getAuthenticator()->authenticate($user);
            Logger::write("User $username logged in.", LogLevel::DEBUG);
            return $this->redirect('index');
        }

        return $this->redirect($this->getServer()->getSecurity()->loginRoute ?? 'login');
    }

    #[Route('logout', '/logout', HttpMethod::GET)]
    public function logout(): ResponseInterface
    {
        $this->getServer()->getSecurity()->getAuthenticator()->quash();
        return $this->redirect('index');
    }

}
