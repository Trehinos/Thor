<?php

/**
 * User forms view and action and list WebController.
 *
 * @package          Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Framework\Actions;

use Thor\Framework\{Managers\UserManager};
use Thor\Debug\{Logger, LogLevel};
use Thor\Security\Identity\DbUser;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Validation\Filters\RegexFilter;
use Thor\Html\{PdoMatrix\PdoMatrix, PdoMatrix\MatrixColumn};
use Thor\Http\{Routing\Route,
    Server\WebServer,
    Response\Response,
    Request\HttpMethod,
    Controllers\WebController,
    Response\ResponseInterface
};

final class Users extends WebController
{

    private UserManager $manager;
    private RegexFilter $usernameFilter;

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
        $this->manager = new UserManager(new CrudHelper(DbUser::class, $this->getServer()->getRequester()));
        $this->usernameFilter = new RegexFilter('/^[A-Za-z0-9]{2,255}$/');
    }

    #[Route('users', '/users', HttpMethod::GET)]
    public function usersInterface(): Response
    {
        return $this->twigResponse(
            'pages/users.html.twig',
            [
                'users'      => $this->manager->getUserCrud()->listAll(),
                /* standby
                'user_table' => (new PdoMatrix(DbUser::class, $this->getServer()->getRequester()))
                    ->getTableHtmlFromRequest(
                        $this->getRequest(),
                        [
                            'public_id' => new MatrixColumn('Public ID'),
                            'username'  => new MatrixColumn('User name'),
                        ]
                    ),
                */
            ]
        );
    }

    #[Route('users-create-form', '/users/create/form', HttpMethod::GET)]
    public function createForm(): Response
    {
        return $this->twigResponse(
            'pages/users_modals/create.html.twig',
            [
                'generatedPassword' => UserManager::generatePassword(),
            ]
        );
    }

    #[Route('users-create-action', '/users/create/action', HttpMethod::POST)]
    public function createAction(): ResponseInterface
    {
        $username = $this->usernameFilter->filter($this->post('username'));
        $clearPassword = $this->post('password');

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

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    #[Route(
        'users-edit-form',
        '/users/$public_id/edit/form',
        HttpMethod::GET,
        ['public_id' => ['regex' => '[A-Za-z0-9-]+']]
    )]
    public function editForm(string $public_id): ResponseInterface
    {
        $user = $this->manager->getUserCrud()->readOneFromPid($public_id);

        return $this->twigResponse(
            'pages/users_modals/edit.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    #[Route(
        'users-edit-action',
        '/users/$public_id/edit/action',
        HttpMethod::POST,
        ['public_id' => ['regex' => '[A-Za-z0-9-]+']]
    )]
    public function editAction(string $public_id): ResponseInterface
    {
        $username = $this->usernameFilter->filter($this->post('username'));

        $errors = [];
        if (!$username) {
            $errors[] = 'too-short-username';
        }

        if (!empty($errors)) {
            Logger::write(print_r($errors, true), LogLevel::DEBUG);
            exit;
        }
        $this->manager->updateUser($public_id, $username);

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    #[Route(
        'users-change-password-form',
        '/users/$public_id/change-password/form',
        HttpMethod::GET,
        ['public_id' => ['regex' => '[A-Za-z0-9-]+']]
    )]
    public function passwordForm(string $public_id): ResponseInterface
    {
        $user = $this->manager->getUserCrud()->readOneFromPid($public_id);

        return $this->twigResponse(
            'pages/users_modals/change-password.html.twig',
            [
                'user'              => $user,
                'generatedPassword' => UserManager::generatePassword(),
            ]
        );
    }

    #[Route(
        'users-change-password-action',
        '/users/$public_id/change-password/action',
        HttpMethod::POST,
        ['public_id' => ['regex' => '[A-Za-z0-9-]+']]
    )]
    public function passwordAction(string $public_id): ResponseInterface
    {
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm-password');

        $errors = [];
        if ($password !== $confirmPassword || strlen($password) < 16) {
            $errors[] = 'bad-password';
            Logger::write("$password <> $confirmPassword");
        }

        if (!empty($errors)) {
            Logger::write(print_r($errors, true), LogLevel::DEBUG);
            exit;
        }
        $this->manager->setPassword($public_id, $password);

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    #[Route(
        'users-delete-action',
        '/users/$public_id/delete/action',
        HttpMethod::POST,
        ['public_id' => ['regex' => '[A-Za-z0-9-]+']]
    )]
    public function deleteAction(string $public_id): ResponseInterface
    {
        $this->manager->deleteOne($public_id);

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

}
