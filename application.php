<?php

/**
 * Thor system
 *      PHP framework and tool
 *
 * @author Sébastien GELDREICH
 * @version 0.1
 * @since 2020-06
 */

require_once __DIR__ . '/engine/vendors/php/autoload.php';
ini_set('display_errors', E_ALL);

use Thor\Globals;
use Thor\HttpKernel;
use Thor\Application;
use Symfony\Component\Yaml\Yaml;

$database = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'database.yml'));

$kernel = null;
switch ($thor_kernel ?? null) {
    case 'http':
        $routes = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'routes.yml'));
        $twig = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'twig.yml'));
        $kernel = new HttpKernel(
            [
                'routes' => $routes,
                'twig' => $twig,
                'database' => $database
            ]
        );
        break;

    case 'cli':
        echo "Not implemented...\n";
        exit;

    default:
        echo "Error\n";
        exit;
}

$app = new Application($kernel);
$app->execute();
