<?php

namespace Thor\Framework\Actions;

use PDOException;
use Thor\Globals;
use Thor\Http\Routing\Route;
use Thor\Http\Response\Response;
use Thor\Web\WebController;
use Thor\Configuration\ThorConfiguration;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Configuration\DatabasesConfiguration;

final class Configuration extends WebController
{

    #[Route('config-config', '/config/general')]
    public function configView(): Response
    {
        $config = ThorConfiguration::get();

        return $this->twigResponse('thor/configuration/config.html.twig', [
            'configuration' => $config,
            'info' => [
                'app_vendor' => 'Marque, société, auteur',
                'app_name' => 'Nom du projet',
                'app_version' => '',
                'app_version_name' => '',
                'env' => 'DEV / DEBUG / PROD : affichage des erreurs et logs',
                'lang' => 'fr / en : langue du système',
                'timezone' => 'Fuseau horaire par défaut pour les opérations de date',
                'log_path' => 'Relatif à <code>' . realpath(Globals::CODE_DIR) . '/</code>',
                'thor_kernel' => 'Kernel actuel'
            ]
        ]);
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
            'status' => $status
        ]);
    }

}
