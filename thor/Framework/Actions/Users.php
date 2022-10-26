<?php

namespace Thor\Framework\Actions;

use Thor\Framework\{Managers\UserManager};
use Thor\Web\WebServer;
use Thor\Tools\DataTables;
use Thor\Web\WebController;
use Thor\Database\Criteria;
use Thor\Framework\Security\DbUser;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Http\Response\ResponseFactory;
use Thor\Configuration\ConfigurationFromFile;
use Thor\Security\Authorization\Authorization;
use Thor\Http\{Routing\Route, Response\Response, Request\HttpMethod, Response\ResponseInterface};

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

    /**
     * @param WebServer $webServer
     *
     * @throws \ReflectionException
     */
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

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Authorization('manage-user')]
    #[Route('users', '/users', HttpMethod::GET)]
    public function usersInterface(): Response
    {
        return $this->twigResponse(
            'thor/pages/users.html.twig',
            [
                'users' => $this->manager->getUserCrud()->listAll(),
            ],
            retrieveMessages: true
        );
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Authorization('manage-user')]
    #[Route('users-table', '/users/table', HttpMethod::GET)]
    public function usersTable(): Response
    {
        return $this->twigResponse(
            'thor/pages/datatable.html.twig',
            [
                'table' => $this->userTable->getDataTable(['hash' => 'Mot de passe']),
            ]
        );
    }

    /**
     * @return Response
     * @throws \Exception
     */
    #[Authorization('manage-user')]
    #[Route('users-table-actions', '/users/table/actions', HttpMethod::POST)]
    public function usersTableActions(): Response
    {
        return ResponseFactory::ok($this->userTable->process($_POST));
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Authorization('manage-user', 'create-user')]
    #[Route('users-create-form', '/users/create/form', HttpMethod::GET)]
    public function createForm(): Response
    {
        return $this->twigResponse(
            'thor/pages/users_modals/create.html.twig',
            [
                'generatedPassword' => UserManager::generatePassword(),
                'permissions'       => UserManager::getPermissions(),
            ]
        );
    }

    /**
     * @return ResponseInterface
     * @throws \Exception
     */
    #[Authorization('manage-user', 'create-user')]
    #[Route('users-create-action', '/users/create/action', HttpMethod::POST)]
    public function createAction(): ResponseInterface
    {
        $username = $this->post('username', '');
        $clearPassword = $this->post('password', '');
        $permissions = $this->post('permissions', []);

        if (strlen($username) < 8) {
            $this->error('too-short-username', 'Utilisateur non créé');
        }
        if (strlen($clearPassword) < 16) {
            $this->error('too-short-password', 'Utilisateur non créé');
        }

        if (!$this->hasMessages()) {
            $this->manager->createUser($username, $clearPassword, $permissions);
        }

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    /**
     * @param string $public_id
     *
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Authorization('manage-user', 'edit-user')]
    #[Route(
        'users-edit-form',
        '/users/$public_id/edit/form',
        HttpMethod::GET,
        ['public_id' => '[A-Za-z0-9-]+']
    )]
    public function editForm(string $public_id): ResponseInterface
    {
        $user = $this->manager->getUserCrud()->readOneBy(new Criteria(['public_id' => $public_id]));

        return $this->twigResponse(
            'thor/pages/users_modals/edit.html.twig',
            [
                'user'        => $user,
                'permissions' => UserManager::getPermissions(),
            ]
        );
    }

    /**
     * @param string $public_id
     *
     * @return ResponseInterface
     */
    #[Authorization('manage-user', 'edit-user')]
    #[Route(
        'users-edit-action',
        '/users/$public_id/edit/action',
        HttpMethod::POST,
        ['public_id' => '[A-Za-z0-9-]+']
    )]
    public function editAction(string $public_id): ResponseInterface
    {
        $username = $this->post('username', '');
        $permissions = $this->post('permissions', []);

        if (strlen($username) < 8) {
            $this->error('too-short-username', "Utilisateur $username non modifié");
        }

        if (!$this->hasMessages()) {
            $this->manager->updateUser($public_id, $username, $permissions);
        }

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    /**
     * @param string $public_id
     *
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route(
        'users-change-password-form',
        '/users/$public_id/change-password/form',
        HttpMethod::GET,
        ['public_id' => '[A-Za-z0-9-]+']
    )]
    public function passwordForm(string $public_id): ResponseInterface
    {
        $user = $this->manager->getUserCrud()->readOneBy(new Criteria(['public_id' => $public_id]));

        return $this->twigResponse(
            'thor/pages/users_modals/change-password.html.twig',
            [
                'user'              => $user,
                'generatedPassword' => UserManager::generatePassword(32),
            ]
        );
    }

    /**
     * @param string $public_id
     *
     * @return ResponseInterface
     */
    #[Route(
        'users-change-password-action',
        '/users/$public_id/change-password/action',
        HttpMethod::POST,
        ['public_id' => '[A-Za-z0-9-]+']
    )]
    public function passwordAction(string $public_id): ResponseInterface
    {
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm-password');

        if ($password !== $confirmPassword) {
            $this->error('bad-password');
        }
        if (!$this->hasMessages()) {
            $this->manager->setPassword($public_id, $password);
        }

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    /**
     * @param string $public_id
     *
     * @return ResponseInterface
     */
    #[Authorization('manage-user', 'remove-user')]
    #[Route(
        'users-delete-action',
        '/users/$public_id/delete/action',
        HttpMethod::POST,
        ['public_id' => '[A-Za-z0-9-]+']
    )]
    public function deleteAction(string $public_id): ResponseInterface
    {
        $this->manager->deleteOne($public_id);

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

    /**
     * @param string $public_id
     *
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route(
        'user-parameters-form',
        '/users/$public_id/parameters/form',
        HttpMethod::GET,
        ['public_id' => '[A-Za-z0-9-]+']
    )]
    public function userParametersFrom(string $public_id): ResponseInterface
    {
        $user = $this->manager->getUserCrud()->readOneBy(new Criteria(['public_id' => $public_id]));

        return $this->twigResponse(
            'thor/pages/users_modals/parameters.html.twig',
            [
                'user' => $user,
                'parameters' => (new ConfigurationFromFile('user-parameters', true))->getArrayCopy()
            ]
        );
    }

    /**
     * @param string $public_id
     *
     * @return ResponseInterface
     */
    #[Route(
        'user-parameters-action',
        '/users/$public_id/parameters/action',
        HttpMethod::POST,
        ['public_id' => '[A-Za-z0-9-]+']
    )]
    public function userParametersAction(string $public_id): ResponseInterface
    {
        $user = $this->manager->getUserCrud()->readOneBy(new Criteria(['public_id' => $public_id]));
        $parameters = (new ConfigurationFromFile('user-parameters', true))->getArrayCopy();

        foreach ($parameters as $parameter) {
            $userParameterValue = $this->post("parameter-{$parameter['name']}");
            $user->setParameter($parameter['name'], $userParameterValue);
        }

        $this->manager->getUserCrud()->updateOne($user);

        return $this->redirect('index', query: ['menuItem' => 'users']);
    }

}
