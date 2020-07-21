<?php

namespace Thor\App\Actions;

use Thor\App\Managers\UserManager;
use Thor\Controller\BaseController;
use Thor\Database\CrudHelper;
use Thor\Http\Response;
use Thor\Http\Server;

use Thor\App\Entities\User;

final class Users extends BaseController
{

    private UserManager $manager;

    public function __construct(Server $server)
    {
        parent::__construct($server);
        $this->manager = new UserManager(new CrudHelper(User::class, $this->getServer()->getRequester()));
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

    public function createForm(): Response
    {
        return $this->view(
            'pages/users_modals/create.html.twig',
            [
                'generatedPassword' => User::generatePassword()
            ]
        );
    }

    public function createAction(): Response
    {
        $username = Server::post(
            'username',
            null,
            FILTER_VALIDATE_REGEXP,
            [
                'options' => ['regexp' => '/[A-Za-z0-9]{4,255}/']
            ]
        );

        $password = Server::post('password', null,);

        $errors = [];
        if (!$username) {
            $errors[] = 'too-short-username';
        }
        if (!$password || strlen($password) < 16) {
            $errors[] = 'too-short-password';
        }

        if (empty($errors)) {
            $this->manager->createUser($username, $password);
        }

        return $this->redirect('users');
    }

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
        $username = Server::post(
            'username',
            null,
            FILTER_VALIDATE_REGEXP,
            [
                'options' => ['regexp' => '/[A-Za-z0-9]{4,255}/']
            ]
        );

        $errors = [];
        if (!$username) {
            $errors[] = 'too-short-username';
        }

        if (empty($errors)) {
            $this->manager->updateUser($public_id, $username);
        }

        return $this->redirect('users');
    }

}
