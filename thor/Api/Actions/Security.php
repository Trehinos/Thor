<?php

/**
 * WebController of security actions (login, check, logout).
 *
 * @package          Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Api\Actions;

use Thor\Debug\{Logger, LogLevel};
use Thor\Http\{Controllers\WebController, Request\HttpMethod, Response\Response, Routing\Route};

final class Security extends WebController
{

    #[Route('login', '/login', HttpMethod::GET)]
    public function login(): Response
    {
        return $this->twigResponse(
            'login.html.twig',
            []
        );
    }

    #[Route('check', '/security/login/action', HttpMethod::POST)]
    public function check(): Response
    {
        $username = $this->post('username');
        $password = $this->post('password');

        // TODO GET USER FROM USERNAME -> THEN
        $token = $this->getServer()->getSecurity()->authenticate($user, $password);

        if ($token) {
            Logger::write("User $username logged in.", LogLevel::DEBUG);
            return $this->redirect('index');
        }

        return $this->redirect($this->getServer()->getSecurity()->loginRoute ?? 'login');
    }

    #[Route('logout', '/logout', HttpMethod::GET)]
    public function logout(): Response
    {
        $this->getServer()->getSecurity()->deleteToken();
        return $this->redirect('index');
    }

}
