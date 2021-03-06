<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Actions;

use Thor\Http\Request;
use Thor\Http\Routing\Route;
use Thor\Api\Managers\UserManager;
use Thor\Html\PdoMatrix\PdoMatrix;
use Thor\Html\PdoMatrix\MatrixColumn;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Debug\Logger;
use Thor\Http\BaseController;
use Thor\Http\Response;
use Thor\Http\Server;

use Thor\Api\Entities\User;
use Thor\Validation\Filters\PostVarRegex;

final class Users extends BaseController
{

    private UserManager $manager;
    private PostVarRegex $usernameFilter;

    public function __construct(Server $server)
    {
        parent::__construct($server);
        $this->manager = new UserManager(new CrudHelper(User::class, $this->getServer()->getRequester()));
        $this->usernameFilter = new PostVarRegex('/^[A-Za-z0-9]{2,255}$/');
    }

    #[Route('users', '/users', Request::GET)]
    public function usersInterface(): Response
    {
        return $this->view(
            'pages/users.html.twig',
            [
                'users'      => $this->manager->getUserCrud()->listAll(),
                'user_table' => (new PdoMatrix(User::class, $this->getServer()->getRequester()))->getTableHtml(
                    Request::createFromServer(),
                    [
                        'public_id' => new MatrixColumn('Public ID'),
                        'username'  => new MatrixColumn('User name'),
                    ]
                )
            ]
        );
    }

    #[Route('users-create-form', '/users/create/form', Request::GET)]
    public function createForm(): Response
    {
        return $this->view(
            'pages/users_modals/create.html.twig',
            [
                'generatedPassword' => UserManager::generatePassword()
            ]
        );
    }

    #[Route('users-create-action', '/users/create/action', Request::POST)]
    public function createAction(): Response
    {
        $username = $this->usernameFilter->filter('username');
        $clearPassword = Server::post('password');

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

        return $this->redirect('index', queryString: 'menuItem=users');
    }

    #[Route('users-edit-form', '/users/$public_id/edit/form', Request::GET, [
        'public_id' => ['regex' => '[A-Za-z0-9-]+']
    ])]
    public function editForm(
        string $public_id
    ): Response {
        $user = $this->manager->getUserCrud()->readOneFromPid($public_id);

        return $this->view(
            'pages/users_modals/edit.html.twig',
            [
                'user' => $user
            ]
        );
    }

    #[Route('users-edit-action', '/users/$public_id/edit/action', Request::POST, [
        'public_id' => ['regex' => '[A-Za-z0-9-]+']
    ])]
    public function editAction(
        string $public_id
    ): Response {
        $username = $this->usernameFilter->filter('username');

        $errors = [];
        if (!$username) {
            $errors[] = 'too-short-username';
        }

        if (!empty($errors)) {
            Logger::write(print_r($errors, true), Logger::LEVEL_DEBUG, Logger::SEVERITY_ERROR);
            exit;
        }
        $this->manager->updateUser($public_id, $username);

        return $this->redirect('index', queryString: 'menuItem=users');
    }

    #[Route('users-change-password-form', '/users/$public_id/change-password/form', Request::GET, [
        'public_id' => ['regex' => '[A-Za-z0-9-]+']
    ])]
    public function passwordForm(
        string $public_id
    ): Response {
        $user = $this->manager->getUserCrud()->readOneFromPid($public_id);

        return $this->view(
            'pages/users_modals/change-password.html.twig',
            [
                'user'              => $user,
                'generatedPassword' => UserManager::generatePassword()
            ]
        );
    }

    #[Route('users-change-password-action', '/users/$public_id/change-password/action', Request::POST, [
        'public_id' => ['regex' => '[A-Za-z0-9-]+']
    ])]
    public function passwordAction(
        string $public_id
    ): Response {
        $password = Server::post('password');
        $confirmPassword = Server::post('confirm-password');

        $errors = [];
        if ($password !== $confirmPassword || strlen($password) < 16) {
            $errors[] = 'bad-password';
            Logger::write("$password <> $confirmPassword");
        }

        if (!empty($errors)) {
            Logger::write(print_r($errors, true), Logger::LEVEL_DEBUG, Logger::SEVERITY_ERROR);
            exit;
        }
        $this->manager->setPassword($public_id, $password);

        return $this->redirect('index', queryString: 'menuItem=users');
    }

    #[Route('users-delete-action', '/users/$public_id/delete/action', Request::POST, [
        'public_id' => ['regex' => '[A-Za-z0-9-]+']
    ])]
    public function deleteAction(
        string $public_id
    ): Response {
        $this->manager->deleteOne($public_id);

        return $this->redirect('index', queryString: 'menuItem=users');
    }

}
