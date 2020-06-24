<?php

/**
 * Thor system
 *      PHP framework and tool
 *
 * @author Sébastien GELDREICH
 * @version 0.1
 * @since 2020-06
 */

use Thor\Globals;

require_once __DIR__ . '/engine/vendors/php/autoload.php';

$thor_env = 'dev';

switch ($thor_kernel ?? null) {
    case 'http':
        require_once Globals::PHP_DIR . 'http_kernel.php';
        break;

    case 'cli':
        echo "Not implemented...\n";
        exit;

    default:
        echo "Error\n";
        exit;
}
