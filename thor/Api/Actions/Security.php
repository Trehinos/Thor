<?php

namespace Thor\Api\Actions;

use Exception;

use Thor\Api\Managers\UserManager;
use Thor\Database\CrudHelper;
use Thor\Debug\Logger;
use Thor\Http\BaseController;
use Thor\Http\Response;
use Thor\Http\Server;
use Thor\Api\Entities\User;

final class Security extends BaseController
{

    private UserManager $manager;

    public function __construct(Server $server)
    {
        parent::__construct($server);
        $this->manager = new UserManager(new CrudHelper(User::class, $this->getServer()->getRequester()));
    }

    /**
     * GET /login
     *
     * @return Response
     *
     * @throws Exception
     */
    public function login(): Response
    {
        return $this->view(
            'login.html.twig',
            []
        );
    }

    /**
     * POST /security/login/action
     *
     * @return Response
     *
     * @throws Exception
     */
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

    /**
     * POST /logout
     *
     * @return Response
     *
     * @throws Exception
     */
    public function logout(): Response
    {
        $this->getServer()->getSecurity()?->deleteToken();
        return $this->redirect('index');
    }

}
