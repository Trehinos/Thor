<?php

/**
 * Thor system
 *      Main : instantiate Application and execute Kernel.
 *
 *  INPUT
 *  $thor_kernel : has to be set by the entry point (HTTP:index.php / CLI:thor.php,daemon.php)
 *
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 * @version 0.4
 * @since 2021-01
 */

require_once __DIR__ . '/vendors/autoload.php';

use Thor\Globals;
use Thor\Debug\Logger;
use Thor\Process\Application;

ini_set('log_errors', E_ALL);
ini_set('error_log', Globals::VAR_DIR . 'errors.log');

$application = Application::create();
Logger::write('#APP:START#');
$application->execute();
Logger::write('#APP:END#');

exit; // make sure this script can't be included with further actions
