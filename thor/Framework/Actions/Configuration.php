<?php

namespace Thor\Framework\Actions;

use PDOException;
use Thor\Globals;
use Thor\FileSystem\File;
use Thor\Http\Routing\Route;
use Thor\Http\Response\Response;
use Thor\Web\WebController;
use Symfony\Component\Yaml\Yaml;
use Thor\Http\Request\HttpMethod;
use Thor\Configuration\ThorConfiguration;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Configuration\ConfigurationFromFile;
use Thor\Configuration\DatabasesConfiguration;
use Thor\Configuration\Configuration as YmlConfiguration;

final class Configuration extends WebController
{

    private const CONFIG_YML = Globals::CONFIG_DIR . 'config.yml';
    private const DATABASE_YML = Globals::CONFIG_DIR . 'database.yml';

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

    #[Route('config-database', '/config/database')]
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

}
