<?php

/**
 * Thor system
 *      PHP framework and tool
 *
 * This file has to be required by an application starter (e.g., web/index.php)
 *
 * @author Sébastien GELDREICH
 * @version 0.1
 * @since 2020-06
 */

require_once __DIR__ . '/vendors/autoload.php';

use Symfony\Component\Yaml\Yaml;

use Thor\Cli\CliKernel;
use Thor\ConfigurationLoader;
use Thor\Debug\Logger;
use Thor\Globals;
use Thor\Http\HttpKernel;
use Thor\Application;

// Create logger and load base configuration
Logger::getDefaultLogger('prod', Globals::CODE_DIR);
['config' => $config, 'database' => $databases] = ConfigurationLoader::loadConfig('config', 'database');
$thor_env = $config['env'] ?? 'debug';
$path = $config['log_path'] ?? 'var/';
Logger::getDefaultLogger($thor_env, Globals::CODE_DIR . $path);
$lang = $config['lang'] ?? 'fr';
$language = Yaml::parseFile(Globals::STATIC_DIR . "langs/$lang.yml");
ini_set('date.timezone', $config['timezone'] ?? 'Europe/Paris');

// Set error reporting level
if ('prod' === $thor_env) {
    ini_set('display_errors', 0);
} elseif ('debug' === $thor_env) {
    ini_set('display_errors', E_ERROR | E_WARNING | E_PARSE);
} else {
    ini_set('display_errors', E_ALL);
}

// Execute kernel
$kernel = null;
$sapi = php_sapi_name();
try {
    switch ($thor_kernel ?? null) {
        case 'http':
            $kernel = new HttpKernel(
                ['databases' => $databases, 'language' => $language] +
                ConfigurationLoader::loadConfig('twig') +
                ConfigurationLoader::loadStatic('routes')
            );
            break;

        case 'cli':
            $kernel = CliKernel::createCli(ConfigurationLoader::loadStatic('commands'));
            break;

        case 'repl':
        case 'automaton':
            echo "Error :\nNot implemented $thor_kernel kernel.\n";
            break;
    }

    $app = new Application($kernel);
    Logger::write('Execute application');
    $app->execute();
    Logger::write('Application executed !');
} catch (Throwable $e) {
    // Catches all uncatched exception to log them in log file.
    Logger::logThrowable($e);
    echo "UNRECOVERABLE ERROR\n";
    if (in_array($thor_env, ['dev', 'debug'])) {
        echo "<pre>";
        throw $e;
    }
}

Logger::write('END ### END', Logger::DEV);

exit; // make sure this script can't be included with further actions
