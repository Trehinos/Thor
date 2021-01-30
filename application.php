<?php

/**
 * Thor system
 *      PHP framework and tool
 *
 * @author Sébastien GELDREICH
 * @version 0.3
 * @since 2021-01
 */

require_once __DIR__ . '/vendors/autoload.php';

use Thor\Globals;
use Thor\Application;
use Thor\Debug\Logger;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents(Globals::CONFIG_DIR . 'config.yml'));

Application::init(
    in_array(
        strtoupper($config['env'] ?? ''),
        ['DEV', 'DEBUG', 'VERBOSE', 'PROD']
    ) ? $config['env'] : 'DEBUG',
    $config['log_path'] ?? 'var/'
);

$application = new Application(Application::getKernel($thor_kernel ?? []));
Logger::write('Execute application');
$application->execute();
Logger::write('Application executed !');

Logger::write('END ### END', Logger::LEVEL_DEV);

exit; // make sure this script can't be included with further actions
