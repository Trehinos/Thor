<?php

namespace Thor\Framework\Actions;

use Thor\Globals;
use Thor\Framework\{Managers\UserManager};
use Thor\Web\WebServer;
use Thor\Web\WebController;
use Symfony\Component\Yaml\Yaml;
use Thor\Security\Authorization\Authorization;
use Thor\Framework\Configurations\LanguageDictionary;
use Thor\Http\{Routing\Route, Request\HttpMethod, Response\ResponseInterface};

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

    /**
     * @param WebServer $webServer
     * @param Route     $route
     */
    public function __construct(WebServer $webServer, Route $route)
    {
        parent::__construct($webServer, $route);
    }

    /**
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Authorization('manage-user')]
    #[Route('manage-permissions', '/permissions/form')]
    public function permissionsForm(): ResponseInterface
    {
        return $this->twigResponse(
            'thor/pages/permissions.html.twig',
            [
                'permissions' => UserManager::getPermissions(),
                'languages'   => UserManager::getLanguages(),
            ]
        );
    }

    /**
     * @return ResponseInterface
     */
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

    /**
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Authorization('manage-permissions', 'create-user')]
    #[Route('permission-line', '/permission/line', HttpMethod::GET)]
    public function addPermissionLine(): ResponseInterface
    {
        return $this->twigResponse(
            'thor/fragments/permission.html.twig',
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
