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

$database = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'database.yml'));
$config = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'config.yml'));

$thor_env = $config['env'] ?? 'dev';
$path = $config['log_path'] ?? 'var/';

$thor_logger = Logger::getDefaultLogger($thor_env, Globals::CODE_DIR . $path);

if ('prod' === $thor_env) {
    ini_set('display_errors', 0);
} elseif ('debug' === $thor_env) {
    ini_set('display_errors', E_ERROR | E_WARNING | E_PARSE);
} else {
    ini_set('display_errors', E_ALL);
}
ini_set('date.timezone', 'Europe/Paris');

$kernel = null;
$sapi = php_sapi_name();
try {
    switch ($thor_kernel ?? null) {
        case 'http':
            if ('cli' === $sapi) {
                Logger::write("PANIC ABORT : HTTP kernel tried to be executed from CLI context.", Logger::PROD);
                exit;
            }
            Logger::write('Start HTTP context');
            Logger::write('Load routes configuration');
            $routes = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'routes.yml'));
            Logger::write('Load twig configuration');
            $twig = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'twig.yml'));
            $lang = $config['lang'] ?? 'fr';
            Logger::write('Load language configuration');
            $language = Yaml::parse(file_get_contents(Globals::ENGINE_DIR . "langs/$lang.yml"));
            $kernel = new HttpKernel(
                [
                    'routes' => $routes,
                    'twig' => $twig,
                    'database' => $database,
                    'language' => $language
                ]
            );
            break;

        case 'cli':
            if ('cli' !== $sapi) {
                Logger::write("PANIC ABORT : CLI kernel tried to be executed from another context.", Logger::PROD);
                exit;
            }
            echo "Not implemented...\n";
            exit;

        default:
            Logger::write("PANIC ABORT : kernel not defined.", Logger::PROD);
            echo "Error :\nKernel not selected.\n";
            exit;
    }

    $app = new Application($kernel);

    Logger::write('Execute application');
    $app->execute();
    Logger::write('Application executed !');
} catch (Throwable $e) {
    $pad = str_repeat(' ', 33);
    $traceStr = '';

    foreach ($e->getTrace() as $trace) {
        $traceStr .= "$pad  • Location : {$trace['file']}:{$trace['line']}\n$pad    Function : {$trace['function']}\n";
    }

    $message = <<<EOT
        ERROR THROWN IN FILE {$e->getFile()} LINE {$e->getLine()} : {$e->getMessage()}
        $pad Trace :
        $traceStr                 
        EOT;
    $thor_logger->log($message, Logger::DEBUG);
    echo "UNRECOVERABLE ERROR\n";
}

$thor_logger->log(str_repeat('#', 90), Logger::DEV);

exit; // make sure this script can't be included with further actions
