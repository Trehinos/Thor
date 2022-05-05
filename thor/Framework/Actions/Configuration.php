<?php

namespace Thor\Framework\Actions;

use PDOException;
use Thor\Globals;
use Thor\Web\WebController;
use Thor\Http\Routing\Route;
use Thor\Http\Response\Response;
use Thor\Http\Request\HttpMethod;
use Thor\Configuration\ThorConfiguration;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Configuration\ConfigurationFromFile;
use Thor\Framework\Configurations\SecurityConfiguration;
use Thor\Framework\Configurations\DatabasesConfiguration;
use Thor\Configuration\Configuration as YmlConfiguration;

final class Configuration extends WebController
{

    private const CONFIG_YML = Globals::CONFIG_DIR . 'config.yml';
    private const DATABASE_YML = Globals::CONFIG_DIR . 'database.yml';
    private const SECURITY_YML = Globals::CONFIG_DIR . 'security.yml';

    #[Route('config-config', '/config/general')]
    public function configView(): Response
    {
        $config = ThorConfiguration::get();

        return $this->twigResponse('thor/configuration/config.html.twig', [
            'configuration' => $config,
            'writable'      => is_writeable(self::CONFIG_YML),
            'filename'      => realpath(self::CONFIG_YML),
            'info'          => [
                'app_vendor'       => 'Marque, société, auteur',
                'app_name'         => 'Nom du projet',
                'app_version'      => '',
                'app_version_name' => '',
                'env'              => 'DEV / DEBUG / PROD : affichage des erreurs et logs',
                'lang'             => 'fr / en : langue du système',
                'timezone'         => 'Fuseau horaire par défaut pour les opérations de date',
                'log_path'         => 'Relatif à <code>' . realpath(Globals::CODE_DIR) . '/</code>',
                'thor_kernel'      => 'Kernel actuel <small><i class="fas fa-info-circle text-info"></i> informatif, ne peut pas être changé</small>',
            ],
        ]);
    }

    #[Route('config-save', '/config-save/general', HttpMethod::POST)]
    public function saveConfig(): Response
    {
        $config = $this->post('config');
        ConfigurationFromFile::writeTo(new YmlConfiguration($config), 'config');

        return $this->redirect('index', query: ['menuItem' => 'config-config']);
    }

    #[Route('database-config', '/config/database')]
    public function databaseView(): Response
    {
        $dbConfig = DatabasesConfiguration::get();

        $status = [];
        foreach (PdoCollection::createFromConfiguration($dbConfig)->all() as $name => $pdoHandler) {
            try {
                $pdoHandler->getPdo();
                $status[$name] = true;
            } catch (PDOException $e) {
                $status[$name] = false;
            }
        }

        return $this->twigResponse('thor/configuration/database.html.twig', [
            'databases' => $dbConfig->getArrayCopy(),
            'writable'  => is_writeable(self::DATABASE_YML),
            'filename'  => realpath(self::DATABASE_YML),
            'status'    => $status,
        ]);
    }

    #[Route('database-save', '/config-save/database', HttpMethod::POST)]
    public function saveDatabase(): Response
    {
        $databases = [];
        foreach (range(1, intval($this->post('size'))) as $index) {
            $databases[$this->post('name', [])[$index]] = [
                'dsn'      => $this->post('dsn', [])[$index],
                'user'     => $this->post('user', [])[$index],
                'password' => $this->post('password', [])[$index],
                'case'     => $this->post('case', [])[$index],
            ];
        }

        ConfigurationFromFile::writeTo(new YmlConfiguration($databases), 'database');

        return $this->redirect('index', query: ['menuItem' => 'database-config']);
    }

    #[Route('security-config', '/config/security')]
    public function securityView(): Response
    {
        $securityConfig = SecurityConfiguration::get();

        return $this->twigResponse(
            'thor/configuration/security.html.twig',
            [
                'security' => $securityConfig,
                'writable'  => is_writeable(self::SECURITY_YML),
                'filename'  => realpath(self::SECURITY_YML),
            ]
        );
    }

    #[Route('security-save', '/config-save/security', HttpMethod::POST)]
    public function saveSecurity(): Response
    {
        // TODO

        return $this->redirect('index', query: ['menuItem' => 'security-config']);
    }

    #[Route('config-not-implemented', '/config/not-implemented')]
    public function notImplemented(): Response
    {
        return $this->twigResponse('thor/page-not-implemented.html.twig');
    }

}
