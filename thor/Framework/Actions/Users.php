<?php

namespace Thor\Framework\Actions;

use Thor\Globals;
use Thor\Framework\{Managers\UserManager};
use Thor\Tools\DataTables;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Symfony\Component\Yaml\Yaml;
use Thor\Debug\{Logger, LogLevel};
use Thor\Security\Identity\DbUser;
use Thor\Factories\ResponseFactory;
use Thor\Database\PdoTable\Criteria;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Configuration\LanguageDictionary;
use Thor\Configuration\ConfigurationFromFile;
use Thor\Security\Authorization\Authorization;
use Thor\Http\{Routing\Route,
    Server\WebServer,
    Response\Response,
    Request\HttpMethod,
    Controllers\WebController,
    Response\ResponseInterface
};

/**
 * User forms view and action and list WebController.
 *
 * @internal
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Users extends WebController
{

    private UserManager $manager;
    private DataTables $userTable;

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
        $this->manager = new UserManager(new CrudHelper(DbUser::class, $this->getServer()->getRequester()));

        $this->userTable = new DataTables(
            DbUser::class,
            $this->getServer()->getHandler(),
            ['public_id', 'id', 'username', 'permissions'],
            [
                'permissions' => [
                    'get' => fn(string $permissions) => '<ul>' . implode(
                            '',
                            array_map(
                                fn(string $permission) => "<li>$permission</li>",
                                json_decode($permissions)
                            )
                        ) . '</ul>',
                ],
            ]
        );
    }

    #[Authorization('manage-user')]
    #[Route('users', '/users', HttpMethod::GET)]
    public function usersInterface(): Response
    {
        return $this->twigResponse(
            'pages/users.html.twig',
            [
                'users' => $this->manager->getUserCrud()->listAll(),
            ]
        );
    }

    #[Authorization('manage-user')]
    #[Route('users-table', '/users/table', HttpMethod::GET)]
    public function usersTable(): Response
    {
        return $this->twigResponse(
            'pages/datatable.html.twig',
            [
                'table' => $this->userTable->getDataTable(['hash' => 'Mot de passe']),
            ]
        );
    }

    #[Authorization('manage-user')]
    #[Route('users-table-actions', '/users/table/actions', HttpMethod::POST)]
    public function usersTableActions(): Response
    {
        return ResponseFactory::ok($this->userTable->process($_POST));
    }

    #[Authorization('manage-user', 'create-user')]
    #[Route('users-create-form', '/users/create/form', HttpMethod::GET)]
    public function createForm(): Response
    {
        return $this->twigResponse(
            'pages/users_modals/create.html.twig',
            [
                'generatedPassword' => UserManager::generatePassword(),
                'permissions'       => UserManager::getPermissions(),
            ]
        );
    }

    #[Authorization('manage-user', 'create-user')]
    #[Route('users-create-action', '/users/create/action', HttpMethod::POST)]
    public function createAction(): ResponseInterface
    {
        $username = $this->post('username');
        $clearPassword = $this->post('password');
        $permissions = $this->post('permissions', []);

        $errors = [];
        if (!$username) {
            $errors[] = 'too-short-username';
        }
        if (!$clearPassword || strlen($clearPassword) < 16) {
            $errors[] = 'too-short-password';
        }

        if (empty($errors)) {
            $this->manager->createUser($username, $clearPassword, $permissions);
        }

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    #[Authorization('manage-user', 'edit-user')]
    #[Route(
        'users-edit-form',
        '/users/$public_id/edit/form',
        HttpMethod::GET,
        ['public_id' => ['regex' => '[A-Za-z0-9-]+']]
    )]
    public function editForm(string $public_id): ResponseInterface
    {
        $user = $this->manager->getUserCrud()->readOneBy(new Criteria(['public_id' => $public_id]));

        return $this->twigResponse(
            'pages/users_modals/edit.html.twig',
            [
                'user'        => $user,
                'permissions' => UserManager::getPermissions(),
            ]
        );
    }

    #[Authorization('manage-user', 'edit-user')]
    #[Route(
        'users-edit-action',
        '/users/$public_id/edit/action',
        HttpMethod::POST,
        ['public_id' => ['regex' => '[A-Za-z0-9-]+']]
    )]
    public function editAction(string $public_id): ResponseInterface
    {
        $username = $this->post('username');
        $permissions = $this->post('permissions', []);

        $errors = [];
        if (!$username) {
            $errors[] = 'too-short-username';
        }

        if (!empty($errors)) {
            Logger::write(print_r($errors, true), LogLevel::DEBUG);
            exit;
        }
        $this->manager->updateUser($public_id, $username, $permissions);

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    #[Authorization('manage-user', 'edit-user')]
    #[Route(
        'users-change-password-form',
        '/users/$public_id/change-password/form',
        HttpMethod::GET,
        ['public_id' => ['regex' => '[A-Za-z0-9-]+']]
    )]
    public function passwordForm(string $public_id): ResponseInterface
    {
        $user = $this->manager->getUserCrud()->readOneBy(new Criteria(['public_id' => $public_id]));

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

    #[Authorization('manage-user', 'remove-user')]
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
