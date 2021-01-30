<?php

namespace App\Actions;

use Exception;

use App\Managers\UserManager;
use Thor\Controller\BaseController;
use Thor\Database\CrudHelper;
use Thor\Debug\Logger;
use Thor\Http\Response;
use Thor\Http\Server;

use App\Entities\User;
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
     * @return Response
     */
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

    /**
     * GET /users/$public_id/edit/form
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

    /**
     * POST /users/$public_id/edit/action
     *
     * @param string $public_id
     *
     * @return Response
     */
    public function editAction(string $public_id): Response
    {
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

    /**
     * GET /users/$public_id/change-password/form
     *
     * @param string $public_id
     *
     * @return Response
     */
    public function passwordForm(string $public_id): Response
    {
        $user = $this->manager->getUserCrud()->readOneFromPid($public_id);

        return $this->view(
            'pages/users_modals/change-password.html.twig',
            [
                'user' => $user,
                'generatedPassword' => UserManager::generatePassword()
            ]
        );
    }

    /**
     * POST /users/$public_id/change-password/action
     *
     * @param string $public_id
     *
     * @return Response
     */
    public function passwordAction(string $public_id): Response
    {
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

    /**
     * POST /users/$public_id/delete/action
     *
     * @param string $public_id
     *
     * @return Response
     */
    public function deleteAction(string $public_id): Response
    {
        $this->manager->deleteOne($public_id);

        return $this->redirect('index', queryString: 'menuItem=users');
    }

}
