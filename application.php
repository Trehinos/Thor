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

use Thor\Globals;
use Thor\Application;
use Thor\Debug\Logger;
use Thor\ConfigurationLoader;
use Thor\Database\DefinitionHelper;
use Thor\Database\PdoExtension\AdvancedPdoRow;

// Load configuration
Application::loadMainConfiguration();
Application::setErrorReporting();

// Set global static state
$path = Application::$config['log_path'] ?? 'var/';
Logger::getDefaultLogger(Application::$thor_env, Globals::CODE_DIR . $path);
AdvancedPdoRow::$definitionHelper = new DefinitionHelper(
    ConfigurationLoader::loadStatic('db_definition')['db_definition']['schema'] ?? []
);

// Start application
Application::executeKernel($thor_kernel ?? null);

exit; // make sure this script can't be included with further actions
