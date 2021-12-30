<?php

namespace Thor\Framework\Actions;

use Thor\Globals;
use Thor\Framework\{Managers\UserManager};
use Thor\Tools\DataTables;
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
 * User permissions view and action and list WebController.
 *
 * @internal
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Permissions extends WebController
{

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
    }

    #[Authorization('manage-user')]
    #[Route('manage-permissions', '/permissions/form')]
    public function permissionsForm(): ResponseInterface
    {
        return $this->twigResponse(
            'pages/permissions.html.twig',
            [
                'permissions' => UserManager::getPermissions(),
                'languages'   => UserManager::getLanguages(),
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
                    UserManager::getPermissionLabelsFunction(),
                    ['']
                ),
                'languages'  => UserManager::getLanguages(),
            ]
        );
    }

}
