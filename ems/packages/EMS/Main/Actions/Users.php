<?php

namespace Packages\EMS\Main\Actions;

use Exception;

use Thor\Database\CrudHelper;
use Thor\Debug\Logger;
use Thor\Http\BaseController;
use Thor\Http\Response;
use Thor\Http\Server;

use Thor\Validation\Filters\PostVarRegex;

use Packages\EMS\Main\Domain\Managers\UserManager;
use Packages\EMS\Main\Entities\User;

final class Users extends BaseController
{

    private UserManager $manager;
    private PostVarRegex $usernameFilter;

    public function __construct(Server $server)
    {
        parent::__construct($server);
        $this->manager = new UserManager(new CrudHelper(User::class, $this->getServer()->getRequester()));
        $this->usernameFilter = new PostVarRegex('/^[A-Za-z0-9]{4,255}$/');
    }

    public function usersInterface(): Response
    {
        return $this->view(
            'pages/users.html.twig',
            [
                'users' => $this->manager->getUserCrud()->listAll()
            ]
        );
    }

    /**
     * GET /users/create/form
     *
     * @return Response
     *
     * @throws Exception
     */
    public function createForm(): Response
    {
        return $this->view(
            'pages/users_modals/create.html.twig',
            [
                'generatedPassword' => UserManager::generatePassword()
            ]
        );
    }

    /**
     * POST /users/create/action
     *
     * TODO : gestion des erreurs en session
     *
     * @return Response
     */
    public function createAction(): Response
    {
        $username = $this->usernameFilter->filter('username');
        $clearPassword = Server::post('password', null);

        $errors = [];
        if (!$username) {
            $errors[] = 'too-short-username';
        }
        if (!$clearPassword || strlen($clearPassword) < 16) {
            $errors[] = 'too-short-password';
        }

        if (empty($errors)) {
            $this->manager->createUser($username, $clearPassword);
        }

        return $this->redirect('users');
    }

    /**
     * GET /users/edit
     *
     * @param string $public_id
     *
     * @return Response
     */
    public function editForm(string $public_id): Response
    {
        $user = $this->manager->getUserCrud()->readOneFromPid($public_id);

        return $this->view(
            'pages/users_modals/edit.html.twig',
            [
                'user' => $user
            ]
        );
    }

    public function editAction(string $public_id): Response
    {
        $username = $this->usernameFilter->filter('username');

        $errors = [];
        if (!$username) {
            $errors[] = 'too-short-username';
        }

        if (!empty($errors)) {
            Logger::write(print_r($errors, true), Logger::DEBUG, Logger::ERROR);
            exit;
        }
        $this->manager->updateUser($public_id, $username);

        return $this->redirect('users');
    }

    public function deleteAction(string $public_id): Response
    {
        $this->manager->deleteUser($public_id);

        return $this->redirect('users');
    }

}
