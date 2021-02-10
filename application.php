<?php

/**
 * Thor system
 *      Main : instantiate Application and execute Kernel.
 *
 *  INPUT
 *  $thor_kernel : has to be set by the entry point (HTTP:index.php/CLI:thor.php)
 *
 * @author Sébastien GELDREICH
 * @version 0.3
 * @since 2021-01
 */

require_once __DIR__ . '/vendors/autoload.php';

use Thor\Thor;
use Thor\Application;
use Thor\Debug\Logger;

$config = Thor::getInstance()->loadConfig('config');

Application::init(
    in_array(
        $env = strtolower($config['env'] ?? ''),
        ['dev', 'debug', 'verbose', 'prod']
    ) ? $env : 'debug',
    $config['log_path'] ?? __DIR__ . '/var/'
);

$application = new Application(Application::getKernel($thor_kernel ?? ''));
Logger::write('Execute application');
$application->execute();
Logger::write('Application executed !');

Logger::write('END ### END', Logger::LEVEL_DEV);

exit; // make sure this script can't be included with further actions
