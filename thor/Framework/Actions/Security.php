<?php

namespace Thor\Framework\Actions;

use Thor\Web\WebController;
use Thor\Debug\{Logger, LogLevel};
use Thor\Http\{Routing\Route, Request\HttpMethod, Response\ResponseInterface};

/**
 * WebController of security routes (login, check and logout).
 *
 * @internal
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Security extends WebController
{

    /**
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('login', '/login', HttpMethod::GET)]
    public function login(): ResponseInterface
    {
        return $this->twigResponse('login.html.twig');
    }

    /**
     * @return ResponseInterface
     */
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

    /**
     * @return ResponseInterface
     */
    #[Route('logout', '/logout', HttpMethod::GET)]
    public function logout(): ResponseInterface
    {
        $this->getServer()->getSecurity()->getAuthenticator()->quash();
        return $this->redirect('index');
    }

}
