<?php

namespace Thor\Framework\Actions;

use PDOException;
use Thor\Http\Routing\Route;
use Thor\Http\Response\Response;
use Thor\Http\Controllers\WebController;
use Thor\Configuration\ThorConfiguration;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Configuration\DatabasesConfiguration;

final class Configuration extends WebController
{

    #[Route('config', '/config/general')]
    public function configView(): Response
    {
        $config = ThorConfiguration::get();

        return $this->twigResponse('thor/configuration/database.html.twig', [
            'configuration' => $config
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
