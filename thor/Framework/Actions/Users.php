<?php

namespace Thor\Framework\Actions;

use Thor\Globals;
use Thor\Framework\{Managers\UserManager};
use Symfony\Component\Yaml\Yaml;
use Thor\Debug\{Logger, LogLevel};
use Thor\Security\Identity\DbUser;
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

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
        $this->manager = new UserManager(new CrudHelper(DbUser::class, $this->getServer()->getRequester()));
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

    #[Authorization('manage-user', 'create-user')]
    #[Route('users-create-form', '/users/create/form', HttpMethod::GET)]
    public function createForm(): Response
    {
        return $this->twigResponse(
            'pages/users_modals/create.html.twig',
            [
                'generatedPassword' => UserManager::generatePassword(),
                'permissions'       => $this->getPermissions(),
            ]
        );
    }

    private function getPermissions(): array
    {
        return array_map(
            $this->getPermissionLabelsFunction(),
            ConfigurationFromFile::get('permissions', true)->getArrayCopy()
        );
    }

    private function getPermissionLabelsFunction(): callable
    {
        return fn(string $permission) => [
            'permission' => $permission,
            'label'      =>
                array_combine(
                    $this->getLanguages(),
                    array_map(
                        fn(string $language) => LanguageDictionary::get($language)['permissions'][$permission]
                            ?? $permission,
                        $this->getLanguages()
                    )
                ),
        ];
    }

    private function getLanguages(): array
    {
        return array_map(
            fn(string $filename) => explode('.', basename($filename))[0],
            glob(Globals::STATIC_DIR . 'langs/*.yml')
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
                'permissions' => $this->getPermissions(),
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

    #[Authorization('manage-user')]
    #[Route('manage-permissions', '/permissions/form')]
    public function permissionsForm(): ResponseInterface
    {
        return $this->twigResponse(
            'pages/permissions.html.twig',
            [
                'permissions' => $this->getPermissions(),
                'languages'   => $this->getLanguages(),
            ]
        );
    }

    #[Authorization('manage-permissions')]
    #[Route('permissions-update', '/permissions/action', HttpMethod::POST)]
    public function permissionsAction(): ResponseInterface
    {
        $permissions = $this->post('permissions');
        $permissionsData = [];
        foreach ($permissions['permission'] as $permission) {
            $permissionsData[] = $permission;
        }
        file_put_contents(
            Globals::STATIC_DIR . "permissions.yml",
            Yaml::dump($permissionsData)
        );
        foreach ($permissions['label'] as $language => $labels) {
            $languageData = LanguageDictionary::get($language);
            $languageData['permissions'] = [];
            foreach ($labels as $key => $label) {
                $languageData['permissions'][$permissionsData[$key]] = $label;
            }
            dump($languageData);
            file_put_contents(
                Globals::STATIC_DIR . "langs/$language.yml",
                Yaml::dump($languageData->getArrayCopy())
            );
        }

        return $this->redirect('index', query: ['menuItem' => 'manage-permissions']);
    }

    #[Authorization('manage-permissions', 'create-user')]
    #[Route('permission-line', '/permission/line', HttpMethod::GET)]
    public function addPermissionLine(): ResponseInterface
    {
        return $this->twigResponse(
            'fragments/permission.html.twig',
            [
                'permission' => array_map(
                    $this->getPermissionLabelsFunction(),
                    ['']
                ),
                'languages'  => $this->getLanguages(),
            ]
        );
    }

}
