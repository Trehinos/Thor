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

use Thor\Debug\Logger;
use Thor\Globals;
use Thor\Http\HttpKernel;
use Thor\Application;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'config.yml'));
$databases = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'database.yml'));

$thor_env = $config['env'] ?? 'debug';
$path = $config['log_path'] ?? 'var/';

Logger::getDefaultLogger($thor_env, Globals::CODE_DIR . $path);

if ('prod' === $thor_env) {
    ini_set('display_errors', 0);
} elseif ('debug' === $thor_env) {
    ini_set('display_errors', E_ERROR | E_WARNING | E_PARSE);
} else {
    ini_set('display_errors', E_ALL);
}
ini_set('date.timezone', $config['timezone'] ?? 'Europe/Paris');

$kernel = null;
$sapi = php_sapi_name();
try {
    switch ($thor_kernel ?? null) {
        case 'http':
            if ('cli' === $sapi) {
                Logger::write("PANIC ABORT : HTTP kernel tried to be executed from CLI context.", Logger::PROD, Logger::ERROR);
                exit;
            }
            Logger::write('Start HTTP context');
            Logger::write('Load routes configuration');
            $routes = Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'routes.yml'));
            Logger::write('Load twig configuration');
            $twig = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'twig.yml'));
            $lang = $config['lang'] ?? 'fr';
            Logger::write('Load language configuration');
            $language = Yaml::parse(file_get_contents(Globals::STATIC_DIR . "langs/$lang.yml"));
            $kernel = new HttpKernel(
                [
                    'routes' => $routes,
                    'twig' => $twig,
                    'databases' => $databases,
                    'language' => $language
                ]
            );
            break;

        case 'cli':
        case 'repl':
        case 'automaton':
            if ('cli' !== $sapi) {
                Logger::write("PANIC ABORT : CLI, REPL or automaton kernel tried to be executed from another context than CLI.", Logger::PROD, Logger::ERROR);
                exit;
            }
            echo "Not implemented...\n";
            break;

        default:
            Logger::write("PANIC ABORT : kernel not defined.", Logger::PROD, Logger::ERROR);
            echo "Error :\nKernel not selected.\n";
            exit;
    }

    $app = new Application($kernel);

    Logger::write('Execute application');
    $app->execute();
    Logger::write('Application executed !');
} catch (Throwable $e) {
    Logger::logThrowable($e);
    echo "UNRECOVERABLE ERROR\n";
}

Logger::write('END ### END', Logger::DEV);

exit; // make sure this script can't be included with further actions
